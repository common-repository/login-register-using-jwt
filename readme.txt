=== WP Login and Register using JWT ===
Contributors: cyberlord92
Tags: jwt, json web token, single sign-on, api, login, sso, wp login, jwt authentication, jwt login, register, rest api, oauth
Requires at least: 3.0.1
Tested up to: 6.5
Stable tag: 2.8.0
Requires PHP: 5.6
License: MIT/Expat
License URI: https://docs.miniorange.com/mit-license

WordPress login (WordPress Single Sign-On) using JWT token obtained from other WordPress sites or applications. Synchronize user sessions between your WordPress and other connected applications [24/7 SUPPORT]

== Description ==

The **[WordPress Login and Register using JWT plugin](https://plugins.miniorange.com/wordpress-login-using-jwt-single-sign-on-sso)** allows you to **log in (Single Sign-On)** into your WordPress application using the **JWT token(JSON Web token)** obtained from any other WordPress site or other applications/platforms including mobile applications. This helps users perform **autologin to WordPress** and **synchronize user sessions** without the need to log in again.

|<a href="https://plugins.miniorange.com/wordpress-login-using-jwt-single-sign-on-sso" target="_blank"> Features </a>| <a href="https://plugins.miniorange.com/wordpress-single-sign-on-using-jwt-token" target="_blank"> WordPress JWT Login Setup Guide </a>|<a href="https://www.youtube.com/playlist?list=PL2vweZ-PcNpevdcrVhs_dQ3qOxc0102wI" target="_blank"> Videos </a>|

**WORDPRESS SINGLE SIGN-ON / SSO ( LOGIN INTO WORDPRESS )**
**WordPress Single Sign-On SSO** also simply called **WordPress SSO** allows you to login into WordPress using the credentials of other platforms. So, the user will just use a single set of credentials to log in to multiple applications.

**WordPress Single Sign-On / SSO using JWT(JSON Web Token)**
**WordPress Single Sign-On (SSO) with JWT** allows you to log into the WordPress site using the user-based JWT token obtained externally when the user authenticates for the first time in any connected external application.
The JWT token authentication is the most popular way of authentication nowadays as it is a secure and lightweight protocol. The JWT token can be obtained either when a user logs into other platforms via **[OAuth](https://oauth.net/)/[OpenID Connect](https://openid.net/connect/)** protocol or can be created explicitly using the user information and secure algorithms. 
With this plugin, you can easily use the user-based JWT token to log a user in rather than asking them to authenticate again.

*Let's take an example* - If you have a WordPress site and mobile app, now if you are logged into the mobile app, now if you try to access the WordPress site, then to access the particular content, the WordPress site will ask for login again and which is not feasible, so with the JWT SSO (JWT Single Sign-On), you can create the JWT token for the user who is already logged into the mobile app and then on accessing the WordPress site, you can pass that JWT token in the request, using which the same user can authenticate and autologin to the WordPress site and hence won't need to enter the credentials again.

It supports possibly all kinds of **JWT tokens (access-token/id-token)** obtained from **OAuth/OpenID Connect** providers like **AWS Cognito**, **Microsoft Azure AD**, **Azure B2C**, **Okta**, **Keycloak**, **ADFS**, **Google**, **Facebook**, **Apple**, **Discord** and popular applications like **Firebase**.

WordPress login using the JWT also called **JWT SSO (Single Sign-On)** can be done from other platforms and applications including mobile apps (android or IOS), an app built with other programming languages like **.NET**, **JAVA**, **PHP**, **JS** etc. 

== Major functionalities ==


**WordPress Login Endpoint to create user-based JWT token**
Plugin provides the following API endpoint, which can be used to authenticate WordPress users and returns a user-based JWT which can be used to create login sessions in WordPress and other external applications.
`
/wp-json/api/v1/mo-jwt
`  

**WordPress Login using JWT**
This feature provides a way to auto-login users in WordPress using JWT obtained in a very secure way either via passing JWT token in the URL as a parameter, in the request header or shared via secured cookies.

**WordPress user register API endpoint to create users in WordPress using API**
This feature provides the following API endpoint to create users in WordPress in an easy way and on successful user registration, you will receive a JWT token in the response which can be used further for user login and WordPress REST API authorization.
`
wp-json/api/v1/mo-jwt-register
` 

**Delete/Remove users from WordPress using the user-based JWT token (JSON Web Token)**
This feature provides an API endpoint using which you can pass the JWT token and can easily delete the user and revoke access.
`
wp-json/api/v1/mo-jwt-delete
` 
More details for the plugin setup can be checked from **[here](https://plugins.miniorange.com/wordpress-single-sign-on-using-jwt-token)**.

== USE CASES == 

* **Login to External applications using WordPress credentials**
If you are looking to authenticate your WordPress users to log in to external applications, then our plugin provides a login API endpoint using which you can easily authenticate WordPress users and can log in the users to those applications.

* **Single Sign-On Users using the JWT token provided by OAuth/OpenID providers**
This WordPress login and register using the JWT plugin supports the WordPress Single Sign On (WordPress SSO) or WordPress login using the user-based JWT token (id-token/access-token) provided by the external OAuth/OpenID Connect providers (like Microsoft Azure AD, Azure B2C, AWS Cognito, Keycloak, Okta, ADFS, Google, Facebook, Apple, Discord and many more..) on login in some other sites/applications using their credentials.
So, the user just needs to log in once on any other sites/platforms and a JWT token will be provided by these providers for those users will then be used further with security to autologin in other platforms.

[youtube https://youtu.be/RR0o80hGvfU]

* **Automatic WordPress login and site access from mobile app web view | Synchronize WordPress session in the mobile app web view**
Suppose you have a mobile application and want to allow users to access their WordPress site content in the mobile app web view which requires a login so asking the users to enter the credentials again won’t be a good user experience. So, our JWT login plugin provides a solution to you in which the user session from the mobile app can be synchronized with the WordPress site and the user can seamlessly access the WordPress site using the user-based JWT token without the need for a WordPress login again.

[youtube https://youtu.be/0QPIjelCWvk]

* **Automatic session synchronization between WordPress and other applications built on React, Node, Next JS, Flutter, Angular, Java, PHP, and C# ....**
Suppose you have a WordPress site connected to any external application built on any framework, then if you want a feature that if a user is logged in to any one application, should be automatically logged in to another as well. This can be easily achieved using the secure JWT.

[youtube https://youtu.be/OMH_FY-xh8Q]

* **Session sharing between WordPress and other applications sharing the same subdomain (hosted on the same domain)**
Suppose you have a WordPress site and other applications hosted on the same subdomain, such that if the user logs in to any one application, then can be auto-logged into other connected applications on that domain using secure cookie-based JWT token sharing. 
an pass the new user details like username, email, name and password(optional), role etc. in the request body and on successful response, your user will get created and the corresponding user-based JWT will be received and the appropriate error response will be returned on the failure.

[youtube https://youtu.be/Lr9spH2PPeY&list=PL2vweZ-PcNpevdcrVhs_dQ3qOxc0102wI]

* **Sync user login sessions between multiple platforms (Session sharing)**
If you have a WordPress site and other applications sharing the same subdomain and you want the feature in which if a user logged into one site (WordPress or another) and on accessing the other site in the same browser, then that user should get logged in automatically (user session to be synchronized). So, this feature is possible to have with our plugin's JWT cookie-based session-sharing feature.

== Features == 

FREE PLAN

*Create JWT feature*

 - **Login API endpoint** to authenticate WordPress users based on username/email and password
 - Supports the JWT token generation using the **HS256 signing algorithm**.
 - JWT token signing with randomly generated secret signing key.
 - Default JWT **token expiration** is 60 minutes.

*User Registration feature*

 - Provide an API endpoint for user registration with the default subscriber role.
 - Provide a user-based JWT token in the success response.
 - No Extra Security key for user registration API.

*User Deletion feature*

 - Provide an API endpoint for user deletion with JWT token validation using the HS256 signing algorithm.
 - No Extra Security key for user deletion API.

*User login feature*

 - Allows WordPress login (SSO) using a user-based JWT token with HS256 signing created using the plugin's Create JWT feature.
 - Retrieve the JWT token from the URL parameter to allow auto-login.
 - Auto redirection on login to the homepage or on the same page/URL from where the autologin is initiated.
 - Default Subscriber role is assigned on login using JWT.


PREMIUM PLAN

*Create JWT feature* 

 - Supports JWT token generation using **HS256** and a securer **RS256 signing algorithm**.
 - JWT token signing with a **custom secret signing key or certificate**.
 - Custom token expiration to expire the token as per your requirement to improvise security.
 - Custom JWT token decryption key.
 - Revoke and invalidate existing user JWT token whenever a new JWT token is generated for a user.

*User Registration feature*

 - Provide an API endpoint for user registration with a custom role.
 - Provide a user-based JWT token in the success response.
 - Extra Security key for user registration API endpoint.

*User Deletion feature*

 - Provide an API endpoint for user deletion with JWT token validation using the HS256 signing algorithm.
 - Extra Security key for user deletion API.

*User login feature*

 - Allows WordPress login (SSO) using a user-based JWT with HS256 signing created either using plugins create JWT feature or a JWT token obtained from an external source.
 - Allows WordPress login using a user-based JWT with RS256 signing validation.
 - Allows WordPress login using a user-based JWT with **JWKS token validation** support.
 - Allows WordPress login using a user-based JWT obtained from an external **OAuth/OpenID Connect** provider.
 - Retrieve the JWT token from the **URL parameter**, **request header** and **cookie** to allow auto-login between platforms.
 - **Auto redirection** on login to the homepage or on the same page/URL from where the autologin is initiated.
 - Auto redirection on login to any custom URL.
 - User **Attribute/Profile** mapping on SSO login.
 - Option to assign any WordPress role rather than default subscriber on SSO login.
 - **Automatic role and group Mapping** to the user who performs SSO using a JWT token.
 - **SSO Login Audit feature** to track the users who perform login using the JWT token.
 - Add-On to **share the user session to other applications** using the JWT token stored in the cookie  


== Other Related Integrations =

**[OAuth Single Sign On – SSO (OAuth Client)](https://wordpress.org/plugins/miniorange-login-with-eve-online-google-facebook/)** - This plugin allows Single Sign On - SSO login in your WordPress site using external OAuth 2.0, OpenID Connect Providers

**[SAML Single Sign On – SSO Login](https://wordpress.org/plugins/miniorange-saml-20-single-sign-on/)** - This plugin allows Single Sign On - SSO login in your WordPress site using external SAML, WS-FED Providers

**[WordPress REST API Authentication](https://wordpress.org/plugins/wp-rest-api-authentication/)** - This plugin protects your WordPress REST API endpoints from unauthorized access using secure **OAuth 2.0**, **JWT authentication**, **Basic authentication**, **Bearer API Key token** and even more.

 == Installation ==

This section describes how to install the WP JWT Login and Register plugin and get it working.

= From your WordPress dashboard =

1. Visit `Plugins > Add New`
2. Search for `JWT Login`. Find and Install the `WP JWT Login and Register ` plugin by miniOrange
3. Activate the plugin

= From WordPress.org =

1. Download WP JWT Login and Register.
2. Unzip and upload the `wp-jwt-login` directory to your `/wp-content/plugins/` directory.
3. Activate WP JWT Login and Register from your Plugins page.


== Privacy ==

This plugin does not store any user data. This plugin uses login.xecurify.com for registration as miniOrange uses login.xecurify.com if the user chooses to register and upgrade to premium. If the user does not want to register then he can continue using the free plugin. (Link to the privacy policy -  https://www.miniorange.com/privacy-policy.pdf )

== Frequently Asked Questions ==

= What is the login using JWT or JWT login? = 
JWT(JSON Web token) login allows you to login into any platform like WordPress using the user-based JWT token rather than passing the actual login credentials. Also, it is a highly secure way to log in as the JWT which consists of user information is signed using highly secure HSA and RSA algorithms.

= What is JWT SSO (JWT Single Sign-On)? = 
JWT SSO(Single Sign-On) or SSO using JWT token allows the user to log in to any platform using one set of credentials and then JWT formed from the logged-in user details can be used to login automatically to other platforms and does not require to enter the credentials again.

= Does this plugin allow auto login users in WordPress from mobile applications = 
Yes, this plugin provides the feature to auto-login users in WordPress sites from mobile applications and also other applications built on Java, React, Node JS, Angular, C#, PHP etc frameworks. using the JWT token. Moreover, this plugin provides other features to redirect the user to some other URLs on login as well.

= Does this plugin allow WordPress user registration and deletion of the REST API endpoint? = 
Yes, the plugin provides both the user registration endpoint (wp-json/api/v1/mo-jwt-register) as well as deletion API (wp-json/api/v1/delete). 

= Can sessions across multiple applications be synchronized using this plugin? =
This plugin provides the feature in which if multiple applications share the same subdomain with WordPress and if you are logged into one platform then accessing any of the other platforms will log in the user automatically without the need to authenticate again. 

= Does this plugin provides session sharing for WordPress site opened in the web view of a mobile application? = 
Yes, that would be possible to achieve with the plugin, so if a user logs into the mobile app and then clicks on the WordPress site URL link, that WordPress page will be opened in the webview and the plugin will help in establishing the session sharing in the webview such that user won't be required to log in again and can access the WordPress page seamlessly.

= I am using AWS Cognito to log in user to my site built using react and want to achieve auto-login in WordPress when the user accesses the WordPress site using an existing AWS Cognito session =
Yes, our plugin's SSO Login using the JWT feature can be used to share the AWS Cognito user session between the WordPress and React apps using JWT. 

== Screenshots ==

1. List of JWT configuration methods.
2. JWT Login settings
3. Create JWT settings
4. Register for JWT settings
5. Delete users with JWT settings

== Changelog ==

= 2.8.0 =
* Compatibility with WordPress 6.5

= 2.7.0 =
* Compatibility with WordPress 6.4
* Usability improvements

= 2.6.0 =
* Compatibility with WordPress 6.3
* Usability improvements

= 2.5.0 =
* Security Enhancement for JWT registration endpoint using the secret key
* UI Updates

= 2.4.0 =
* Compatibility with WordPress 6.2 and PHP 8.2.*
* Bug and Security Fixes
* UI Updates


= 2.3.0 =
* Compatibility with WordPress 6.0
* Minor Bug Fixes

= 2.2.0 = 
* Major UI Updates
* Added the functionalities for user registration and deletion.
* Bug Fixes and usability improvements
* Compatibility with WordPress 5.9.* and PHP 8+ 

= 2.1.2 = 
* Security Fixes
* Compatibility with WordPress 5.8.1
* Readme Updates

= 2.1.1 = 
* Minor bug fixes
* Readme Changes

= 1.0.0 =  
* First release of the plugin
* Compatibility with WordPress 5.8

== Upgrade Notice ==

= 1.0.0 =  
* First release of the plugin
* Compatibility with WordPress 5.8