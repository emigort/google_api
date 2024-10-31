## Google API

This is just to have a template how to do the Google OAUTH2 and play with the different Google API
- Modify user table as your convenience, this was just for testing 
- Add security as necessary for examples middlewares... etc.

### ENV FIle
- GOOGLE_CONFIG_JSON path to the json config file generated in the OAUTH consent screen in Google

### Routes
- POST       api/google/auth/login .................................................. GoogleController@postLogin
- GET|HEAD   api/google/login/url ............................................. GoogleController@getAuthUrl
- POST       api/google/youtube/broadcast ..................................... YoutubeController@createBroadcast
- DELETE     api/google/youtube/broadcast/{broadcast_id} ......... YoutubeController@deleteBroadcast

## Steps for OAUTH2
1. First get the login url with endpoint `api/google/login/url` and authorize the user in google
2. Get the `code` generated in the authorization in the query parameter of the url generated after the authorization is done and use `api/google/auth/login`
3. Using the refresh token to continously generate access tokens to always stay authenticated
