The Dancing Rabbit Gallery Theme
================

## Changelog
#### 1.1.7 - 2020-12-11
* Fixed issue with the category next/previous buttons. At some point the session had stopped working correctly because of WP changes. Increased the priority of the dst_start_session function and moved the code to determine the category there. Added a session_write_close to prevent WP issues with active sessions.
