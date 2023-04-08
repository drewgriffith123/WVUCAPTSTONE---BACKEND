

# WVU Players Companion - Backend API - User Manual
Manual goes over how to use the api in conjunciton with the native ios application.

Note: 
Send all traffic here.
### Domain_Name = https://restapi-playerscompanion.azurewebsites.net


# Manual
Parameters in a url look like: 
**/filePath?{variable}={value}&{variable}={value}...** 
(Continued for however many parameters needed.)


# Login, Logout, & Create an Account 
All auth methods below
## Login | /users/auth.php
To have a user create an account we need to send the api the information of the user through the parameters in the URL.

Send a get request to **/users/auth.php** from our react-native app.
With the following parameters:

 - **action=createaccount**
 - **name={username}**
 - **password={password}**
 - **firstname={firstname}**
 - **lastname={lastname}**
 - **middlename={middlename}**
 - **type={P/T/AT/G}** P - Player ; ST - Student Trainer ; AT - Athletic Trainer ; G - Developer
 - **playernumber={playersJersey#}**
 - **code={code}**

EXAMPLE

    https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=createaccount&name=grantiscool&password=22222222&firstname=Grant&lastname=Holzemer&middlename=Perry&type=P&playernumber=999&code=001

https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=createaccount&name=grantiscool&password=22222222&firstname=Grant&lastname=Holzemer&middlename=Perry&type=P&playernumber=999&code=001

Note: This account has already been created so the response from the URL will be "Username already exists"

**Return Types**
`"True"` - If user creation was successful.
`"Username already exists"` - Pretty intuitive on what happened
`"False"` - If you gave the wrong number of parameters/wrong data types/other stuff.

## Login |  /users/auth.php
All we need to send to the API for a user to login is the action variable value 'login' and there username and password.

Send a get request to **/users/auth.php** from our react-native app.
With the following parameters:

 - **action=login**
 - **name={username}**
 - **password={password}**

EXAMPLE: 

    https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=login&name=grantiscool&password=22222222

https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=login&name=grantiscool&password=22222222

**Return Types**
If authentification is successful you will receive a session_token that looks something like this:

    9913f272ed8a08587cefb45634e15ef1788531f119a663acd505406962f72e1a

`"Username does not exist. Create account."` - Must create account

`"Invalid Credentials"` - Password is wrong

`"Already active session"` - User already logged in

## Logout | /users/auth.php
All we need to send to the API for a user to logout is the action variable value 'logout' and their UserID.

 - **action=logout**
 - **userid={ID}**

**This will be updated in the future by providing the API with the username instead of the userid**

You can get the userid via the user data methods below.

EXAMPLE:

    https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=logout&userid=15
https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=logout&userid=15



# User Data
All ways to get a users assigned workouts / user info

**All requests to this endpoint must have the bearer token in the authorization header of the get request. You will not be able to get information if the user does not have a active session.**

To get a session token from the user you must have the user login.

React-Native example code:

    fetch('https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=userinfo
    ', {
    	headers: {
    		'Authorization': 'Bearer ' + session_token
    	}
    })
    .then(response => {
    // handle response
    
    })
    .catch(error => {
    // handle error
    
    });

The auth string should look like this:

    Bearer 9913f272ed8a08587cefb45634e15ef1788531f119a663acd505406962f72e1a

## Get UserInfo | /users/users.php
This returns the user info such as (Name, Player Number, Account Type...)

All you need to send to the API is the action variable with value 'userinfo' and you must include the authorization token in the auth header.

 - **action=userinfo**
+ **session_token in auth header**

EXAMPLE: 

    https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=userinfo

**Return Types**

`"Missing Authorization Header."` - Must include session_token in auth header.

`"Session expired. Please login again."` - Users session expired. Typically this is if the user reached the 8hr session limit or if the user has logged out.

If there is a active session you will receive a json object like below:

    {
    "UserId": 15,
    "FirstName":"Grant",
    "MiddleName": "Perry",
    "LastName": "Holzemer",
    "UserType": "P",
    "Username": "grantiscool",
    "Password": "bae5e3208a3c700e3db642b6631e95b9",
    "PlayerNumber": 999999999,
    "Code": 99999999,
    "Position": null
    }


