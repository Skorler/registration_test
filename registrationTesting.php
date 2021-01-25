<?php
require 'vendor/autoload.php';

use Facebook\WebDriver\Cookie;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class registrationTesting extends TestCase
{
    protected RemoteWebDriver $webDriver;

    public function build_browser_capabilities($browser)
    {
        switch ($browser) {
            case 'chrome':
                $options = new ChromeOptions();
                $options->addArguments(array('--incognito'));
                $capabilities = DesiredCapabilities::chrome();
                $capabilities->setCapability('acceptInsecureCerts', true);
                $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
                break;
            case 'firefox':
                $capabilities = DesiredCapabilities::firefox();
                $capabilities->setCapability('acceptInsecureCerts', true);
                break;
                // Edge currently not working
//            case 'edge':
//                $capabilities = DesiredCapabilities::microsoftEdge();
//                $capabilities->setCapability('acceptInsecureCerts', true);
//                break;
                // Opera currently not working
//            case 'opera':
//                $options = new ChromeOptions();
//                $options->addArguments(array('--incognito'));
//                $options->setBinary('D:\Programms\Opera\73.0.3856.345\opera.exe');
//                $capabilities = DesiredCapabilities::opera();
//                $capabilities->setCapability('acceptInsecureCerts', true);
//                $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
//                break;
        }
        return $capabilities;
    }

    public function setUp(): void
    {
        $capabilities = $this->build_browser_capabilities('chrome');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function tearDown(): void
    {
        $this->webDriver->quit();
    }

    public function fillFieldBySeleniumId($seleniumId, string $input): void
    {
        $this->webDriver->findElement(WebDriverBy::cssSelector("[selenium_id='$seleniumId']"))
            ->click()
            ->sendKeys($input);
    }

    public function fillDataFieldBySeleniumId($seleniumId, string $input): void
    {
        $this->webDriver->findElement(WebDriverBy::cssSelector("[selenium_id='$seleniumId']"))
            ->click()
            ->sendKeys(\Facebook\WebDriver\WebDriverKeys::HOME)
            ->sendKeys($input);
    }

    public function clickBySeleniumId($seleniumId): void
    {
        $this->webDriver->findElement(WebDriverBy::cssSelector("[selenium_id='$seleniumId']"))
            ->click();
    }

    public function scroll() : void
    {
        $this->webDriver->executeScript("javascript:window.scrollBy(250,350)");
    }

    public function switchWindow($windowHandlesBefore) : void
    {
        sleep(1);
        $windowHandlesAfter = $this->webDriver->getWindowHandles();
        $newWindowHandle = array_diff($windowHandlesAfter, $windowHandlesBefore);
        $this->webDriver->switchTo()->window(reset($newWindowHandle));
    }

    public function generatePhoneNumber() : string
    {
        return '000' . mt_rand(1000000, 9999999);
    }

    /*
    * @test
    */
    public function test_registerUser()
    {
        $this->webDriver->get("https://landing1.ryabina10.fortest.org/");
        $this->webDriver->manage()->addCookie(new Cookie('RegisterTest', 'Y'));
        $this->webDriver->manage()->window()->maximize();

        $windowHandlesBefore = $this->webDriver->getWindowHandles();
        $this->clickBySeleniumId('no_affiliate_checkbox');
        $this->clickBySeleniumId('homepage_submit');

        $this->switchWindow($windowHandlesBefore);

        $this->fillFieldBySeleniumId("phone", $this->generatePhoneNumber());
        $this->fillFieldBySeleniumId("last_name", 'фамилия');
        $this->fillFieldBySeleniumId("name", 'имя');
        $this->scroll();
        $this->fillFieldBySeleniumId("middle_name", 'отчество');
        $this->fillDataFieldBySeleniumId("date_of_birth", '11111111');

        // Passport data
        $this->fillFieldBySeleniumId("passport_series", '1111');
        $this->fillFieldBySeleniumId("passport_number", '111111');
        $this->fillFieldBySeleniumId("division_code", '111111');
        $this->fillDataFieldBySeleniumId("date_passport_issue", '11111111');
        $this->scroll();

        //KOSTIL for checkboxes
        $this->webDriver->executeScript("
        var checkboxes = document.getElementsByName('checkbox');
        checkboxes[0].checked = true;
        checkboxes[1].checked = true;
        ");

        $this->clickBySeleniumId('register_submit');

        sleep(15);

        $this->clickBySeleniumId('oferta');
        $this->clickBySeleniumId('confirm');
        sleep(1);
        $this->clickBySeleniumId('no_card');
        $this->clickBySeleniumId('no_card_submit');

        print ($this->webDriver->getTitle());
        $this->assertEquals('Первый заем под 0%', $this->webDriver->getTitle());
    }
}