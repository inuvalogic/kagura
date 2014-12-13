Kagura - an Image Serve PHP Class
=======================================
Author: Wisnu Hafid <www.wisnu-hafid.net>

6 May 2013

Change Log:

v1

6 May 2013

- add extension support: jpg, jpeg
- smart resizing
- automatic detect picture ratio
- load database config mode and size

v1.1

23 February 2014

- add extension support: gif, png
- transparency support

how to use:

www.yourwebsite.com/img/mode/size/filename.ext

www.yourwebsite.com/img/mode/filename.ext

mode = path on /images folder and config name on database

path: images/mode

create config code on database: mode_width, mode_height

if file is not exists, none.jpg will show

example:

file real path = /public_html/images/test/testfile.jpg

config code on database: test_width = 200, test_height = 150

http://www.yourwebsite.com/img/test/testfile.jpg -> will be generate to given config size (200x150 px)

http://www.yourwebsite.com/img/test/small/testfile.jpg -> will be generate to small size

http://www.yourwebsite.com/img/test/big/testfile.jpg -> will be generate to big size
