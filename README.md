# Selenium UI Test for XE
* http://www.slideshare.net/akasima/selenium-forxe-33933642

### install
* `git clone` this repository
* `composer install`
	- [composer](https://getcomposer.org/)
* copy `src/XE/UITest/Selenium/config.sample.php` to `src/XE/UITest/Selenium/config.php` and edit.
* make directory to `src/XE/UITest/Selenium/files` and set permission to 707

### test 실행
* `vendor/bin/phpunit -c app/phpunit.xml`

### 설정파일을 변경하여 test 실행
* `XE_SELENIUM_CONFIG=/config_file_name.php vendor/bin/phpunit -c app/phpunit.xml`
 - config_file_name.php 은 src/XE/UITest/Selenium/ 경로에 있어야 합니다.
 
### DEMO
* http://www.youtube.com/watch?v=tAVjw2rr3II
