------------------------------------------------------------------------
CHANGELOG Clevercube
========================================================================
------------------------------------------------------------------------

- Fixed cookie check (#9) and cleaned up the redirection part;
- Refreshing disabled error or logout page does not redirect to the
  login page (#7);
- Logout task no longer redirects to the login page when the logout page
  is disabled (#6);
- Replace CRLF with LF and fix some spacing errors;
- Add user login to AR plug-in example API.

RELEASE 0.3
-----------------
- Compatibility to 0.9 with backwards compatibility to 0.8 (#2, #3);
- Pushed year to 2013 in LICENSE and headers;
- Fixed `null` login page redirection to failed page (#4).

RELEASE 0.2
-----------------
- Various documentation fixes;
- Added missing `_task` input field to External authentication plug-in
  example;
- Fixed the errors when login fails via the External authentication
  plug-in.

RELEASE 0.1
-----------------
- Initialization of the project;
- Added External database, External authentication and Autoresponder
  manager plug-ins.
