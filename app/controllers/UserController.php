<?php
/**
 * 	Controller for user actions
 */
class UserController extends BaseController{

	public function __construct(){
		parent::__construct();
	}

	/**
	*	getIndex
	*
	*	Retrieve user by id and display user page
	*		Passes User $user to the view
	*
	*	@param User $user
	*	@return Illuminate\View\View
	*/
	public function getIndex(User $user){
		//Set data array
		$data = array(
			'user'			=> $user,
			'page_id'		=> 'user_profile',
			'page_title'	=> $user->fname . ' ' . substr($user->lname, 0, 1) . "'s Profile"
		);

		//Render view and return
		return View::make('user.index', $data);
	}

	/**
	*	getEdit
	*	
	*	Allow user to edit their profile
	*		Passes User $user to the view
	*
	*	@param User $user
	*	@return Illuminate\View|View
	*/
	public function getEdit(User $user){
		if(!Auth::check()){
			return Redirect::to('user/login')->with('error', 'Please log in to edit user profile');
		}else if(Auth::user()->id != $user->id){
			return Redirect::back()->with('error', 'You do not have access to that profile.');
		}else if($user == null){
			return Response::error('404');
		}

		//Set data array
		$data = array(
			'page_id'			=> 'edit_profile',
			'page_title'	=> 'Edit Your Profile',
			'user'				=> $user
		);

		return View::make('user.edit.index', $data);
	}

	/**
	*	putEdit
	*
	*	User's put request to update their profile
	*
	*	@param User $user
	*	@return Illuminate\Http\RedirectResponse
	*/
	public function putEdit(User $user){
		if(!Auth::check()){
			return Redirect::to('user/login')->with('error', 'Please log in to edit user profile');
		}else if(Auth::user()->id != $user->id){
			return Redirect::back()->with('error', 'You do not have access to that profile.');
		}else if($user == null){
			return Response::error('404');
		}

		if(strlen(Input::get('password_1')) > 0 || strlen(Input::get('password_2')) > 0){
			if(Input::get('password_1') !== Input::get('password_2')){
				return Redirect::to('user/edit/' . $user->id)->with('error', 'The passwords you\'ve entered do not match.');
			}
			else{
				$password = Input::get('password_1');
			}
		}

		$verify = Input::get('verify');

		$user->email = Input::get('email');
		$user->fname = Input::get('fname');
		$user->lname = Input::get('lname');
		$user->url = Input::get('url');
		$user->phone = Input::get('phone');
		$user->verify = $verify;
		
		// Don't allow oauth logins to update the user's data anymore,
		// since they've set values within Madison.
		$user->oauth_update = false;
		if(!$user->save()){
			return Redirect::to('user/edit/' . $user->id)->withInput()->withErrors($user->getErrors());
		}

		if(isset($verify)){
			$meta = new UserMeta();
			$meta->meta_key = 'verify';
			$meta->meta_value = 'pending';
			$meta->user_id = $user->id;
			$meta->save();

			Event::fire(MadisonEvent::VERIFY_REQUEST_USER, $user);
			
			return Redirect::back()->with('success_message', 'Your profile has been updated')->with('message', 'Your verified status has been requested.');
		}

		return Redirect::back()->with('success_message', 'Your profile has been updated.');
	}

	/**
	*	putIndex
	*
	*	Returns 404 Response
	*
	*	@param $id
	*	@return Response
	*	@todo Remove route and method
	*/
	public function putIndex($id = null){
		return Response::error('404');
	}

	/**
	*	postIndex
	*
	*	Returns 404 Response
	*
	*	@param $id
	*	@return Response
	*	@todo remove route and method
	*/
	public function postIndex($id = null){
		return Response::error('404');
	}

	/**
	*	getLogin
	*
	*	Returns the login page view
	*
	*	@param void
	*	@return Illuminate\View\View
	*/
	public function getLogin(){
		$previous_page = Input::old('previous_page');

		if(!isset($previous_page)){
			$previous_page = URL::previous();
		}

		$data = array(
			'page_id'		=> 'login',
			'page_title'	=> 'Log In',
			'previous_page'	=> $previous_page
		);

		return View::make('login.index', $data);
	}

	/**
	*	postLogin
	*
	*	Handles POST requests for users logging in
	*
	*	@param void
	*	@return Illuminate\Http\RedirectResponse
	*/
	public function postLogin(){
		//Retrieve POST values
		$email = Input::get('email');
		$password = Input::get('password');
		$previous_page = Input::get('previous_page');
		$remember = Input::get('remember');
		$user_details = Input::all();

		//Rules for login form submission
		$rules = array('email' => 'required', 'password' => 'required');
		$validation = Validator::make($user_details, $rules);

		//Validate input against rules
		if($validation->fails()){
			return Redirect::to('user/login')->withInput()->withErrors($validation);
		}

		//Check that the user account exists
		$user = User::where('email', $email)->first();

		if(!isset($user)){
			return Redirect::to('user/login')->with('error', 'That email does not exist.');
		}

		//If the user's token field isn't blank, he/she hasn't confirmed their account via email
		if($user->token != ''){
			return Redirect::to('user/login')->with('error', 'Please click the link sent to your email to verify your account.');
		}

		//Attempt to log user in
		$credentials = array('email' => $email, 'password' => $password);

		if(Auth::attempt($credentials, ($remember == 'true') ? true : false)){
			Auth::user()->last_login = new DateTime;
			Auth::user()->save();
			if(isset($previous_page)){
				return Redirect::to($previous_page)->with('message', 'You have been successfully logged in.');
			}else{
				return Redirect::to('/docs/')->with('message', 'You have been successfully logged in.');
			}
		}
		else{
			return Redirect::to('user/login')->with('error', 'Incorrect login credentials')->withInput(array('previous_page' => $previous_page));
		}
	}

	/**
	 * 	getSignup
	 *
	 *	Returns signup page view
	 *
	 *	@param void
	 *	@return Illuminate\View\View
	 */
	public function getSignup(){
		$data = array(
			'page_id'		=> 'signup',
			'page_title'	=> 'Sign Up for Madison'
		);

		return View::make('login.signup', $data);
	}

	/**
	 * 	postSignup
	 *
	 *	Handles POST requests for users signing up natively through Madison
	 *		Fires MadisonEvent::NEW_USER_SIGNUP Event
	 *
	 *	@param void
	 *	@return Illuminate\Http\RedirectResponse
	 */
	public function postSignup(){
		//Retrieve POST values
		$email = Input::get('email');
		$password = Input::get('password');
		$fname = Input::get('fname');
		$lname = Input::get('lname');
		
		//Create user token for email verification
		$token = str_random();

		//Create new user
		$user = new User();
		$user->email = $email;
		$user->password = $password;
		$user->fname = $fname;
		$user->lname = $lname;
		$user->token = $token;
		if( ! $user->save() ){
			return Redirect::to('user/signup')->withInput()->withErrors($user->getErrors());
		}
			
		Event::fire(MadisonEvent::NEW_USER_SIGNUP, $user);
			
		//Send email to user for email account verification
		Mail::queue('email.signup', array('token'=>$token), function ($message) use ($email, $fname) {
			$message->subject('Welcome to the Madison Community');
			$message->from('sayhello@opengovfoundation.org', 'Madison');
			$message->to($email); // Recipient address
		});

		return Redirect::to('user/login')->with('message', 'An email has been sent to your email address.  Please follow the instructions in the email to confirm your email address before logging in.');
	}

	/**
	 * 	getVerify
	 *
	 *	Handles GET requests for email verifications
	 *
	 *	@param string $token
	 *	@return Illuminate\Http\RedirectRequest
	 */
	public function getVerify($token){
		echo $token;
		$user = User::where('token', $token)->first();

		if(isset($user)){
			$user->token = '';
			$user->save();

			Auth::login($user);

			return Redirect::to('/')->with('success_message', 'Your email has been verified and you have been logged in.  Welcome ' . $user->fname);
		}else{
			return Redirect::to('user/login')->with('error', 'The verification link is invalid.');
		}

	}

	/**
	 * getFacebookLogin
	 *
	 *	Handles OAuth communication with Facebook for signup / login
	 *		Calls $this->getAuthorizationUri() if the oauth code is passed via Input
	 *		Otherwise calls $fb->getAuthorizationUri()
	 *
	 *	@param void
	 *	@return Illuminate\Http\RedirectResponse || $this->oauthLogin($user_info)
	 *	@todo clean up this doc block
	 */
	public function getFacebookLogin(){

		// get data from input
		$code = Input::get('code');

		// get fb service
		$fb = OAuth::consumer('Facebook');

		// check if code is valid

		// if code is provided get user data and sign in
		if(!empty($code)){

			// This was a callback request from facebook, get the token
			$token = $fb->requestAccessToken( $code );

			// Send a request with it
			$result = json_decode( $fb->request( '/me' ), true );

			// Remap the $result to something that matches our schema.
			$user_info = array(
				'fname' => $result['first_name'],
				'lname' => $result['last_name'],
				'email' => $result['email'],
				'oauth_vendor' => 'facebook',
				'oauth_id' => $result['id']
			);

			return $this->oauthLogin($user_info);
		}
		// if not ask for permission first
		else{
			// get fb authorization
			$url = $fb->getAuthorizationUri();

			// return to facebook login url
			 return Redirect::to( (string)$url );
		}
	}

	/**
	 * getFacebookLogin
	 *
	 *	Handles OAuth communication with Twitter for signup / login
	 *		Calls $this->oauthLogin() if the oauth code is passed via Input
	 *		Otherwise calls $tw->requestRequestToken()
	 *
	 *	@param void
	 *	@return Illuminate\Http\RedirectResponse || $this->oauthLogin($user_info)
	 *	@todo clean up this doc block
	 */
	public function getTwitterLogin(){

	    // get data from input
	    $token = Input::get( 'oauth_token' );
	    $verify = Input::get( 'oauth_verifier' );

	    // get twitter service
	    $tw = OAuth::consumer( 'Twitter' );

	    // check if code is valid

	    // if code is provided get user data and sign in
	    if ( !empty( $token ) && !empty( $verify ) ) {

        // This was a callback request from twitter, get the token
        $token = $tw->requestAccessToken( $token, $verify );

        // Send a request with it
        $result = json_decode( $tw->request( 'account/verify_credentials.json' ), true );

				$user_info = array(
					'fname' => $result['name'],
					'lname' => '-',
					'oauth_vendor' => 'twitter',
					'oauth_id' => $result['id']
				);

				return $this->oauthLogin($user_info);
	    }
	    // if not ask for permission first
	    else {
	        // get request token
	        $reqToken = $tw->requestRequestToken();

	        // get Authorization Uri sending the request token
	        $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));

	        // return to twitter login url
	        return Redirect::to( (string)$url );
	    }
	}

	/**
	 * getFacebookLogin
	 *
	 *	Handles OAuth communication with Facebook for signup / login
	 *		Calls $this->oauthLogin() if the oauth code is passed via Input
	 *		Otherwise calls $linkedinService->getAuthorizationUri()
	 *
	 *	@param void
	 *	@return Illuminate\Http\RedirectResponse || $this->oauthLogin($user_info)
	 *	@todo clean up this doc block
	 */
	public function getLinkedinLogin(){

        // get data from input
        $code = Input::get( 'code' );

        $linkedinService = OAuth::consumer( 'Linkedin' );


        if ( !empty( $code ) ) {

			    // retrieve the CSRF state parameter
			    $state = isset($_GET['state']) ? $_GET['state'] : null;

			    // This was a callback request from linkedin, get the token
			    $token = $linkedinService->requestAccessToken($_GET['code'], $state);

            // Send a request with it. Please note that XML is the default format.
            $result = json_decode($linkedinService->request('/people/~:(id,first-name,last-name,email-address)?format=json'), true);

					// Remap the $result to something that matches our schema.
					$user_info = array(
						'fname' => $result['firstName'],
						'lname' => $result['lastName'],
						'email' => $result['emailAddress'],
						'oauth_vendor' => 'linkedin',
						'oauth_id' => $result['id']
					);

					return $this->oauthLogin($user_info);

        }// if not ask for permission first
        else {
            // get linkedinService authorization
            $url = $linkedinService->getAuthorizationUri();

            // return to linkedin login url
            return Redirect::to( (string)$url );
        }
    }

	/**
	*	oauthLogin
	*
	* Use OAuth data to login user.  Create account if necessary.
	*
	*	@param array $user_info
	*	@return Illuminate\Http\RedirectResponse
	*	@todo Should this be moved to the User model?
	*/
	public function oauthLogin($user_info){
		// See if we already have a matching user in the system
		$user = User::where('oauth_vendor', $user_info['oauth_vendor'])
			->where('oauth_id', $user_info['oauth_id'])->first();

		if(!isset($user)){

			// Make sure this user doesn't already exist in the system.
			if(isset($user_info['email'])){
				$existing_user = User::where('email', $user_info['email'])->first();

				if(isset($existing_user)){
					Auth::login($existing_user);

					return Redirect::to('/')->with('success_message', 'Logged in with email address ' . $existing_user->email);
				}
			}

			// Create a new user since we don't have one.
			$user = new User();
			$user->oauth_vendor = $user_info['oauth_vendor'];
			$user->oauth_id = $user_info['oauth_id'];
			$user->oauth_update = true;

			$new_user = true;
		}

		// Now that we have a user for sure, update the user and log them in.
		$user->fname = $user_info['fname'];
		$user->lname = $user_info['lname'];
		if(isset($user_info['email'])){
			$user->email = $user_info['email'];
		}

		// If the user is new, or if we are allowed to update the user via oauth.
		// Note: The oauth_update flag is turned to off the first time the user
		// edits their account within Madison, locking in their info.
		if(isset($new_user) || (isset($user->oauth_update) && $user->oauth_update == true)) {
			if(!$user->save()){
				Log::error('Unable to save user: ', $user_info);
			}
		}

		if($user instanceof User){
			Auth::login($user);	
		}else{
			Log::error('Trying to authenticate user of incorrect type', $user->toArray());
		}
		

		if(isset($new_user)){
			$message = 'Welcome ' . $user->fname;
		}
		else{
			$message = 'Welcome back, ' . $user->fname;
		}

		return Redirect::to('/')->with('success_message', $message);
	}
}
