<?php
require 'vendor/autoload.php';

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

    public function build_browser_capabilities($browser){
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
            case 'explorer':
                $capabilities = DesiredCapabilities::internetExplorer();
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
    /*
    * @test
    */
    public function test_searchTextOnGoogle()
    {
        $this->webDriver->get("https://landing1.ryabina10.fortest.org/");
        $this->webDriver->manage()->window()->maximize();

        $windowHandlesBefore = $this->webDriver->getWindowHandles();

        $this->webDriver->findElement(WebDriverBy::className("indexCheckboxNotAf"))
            ->click();
        $this->webDriver->findElement(WebDriverBy::className("prevent-register"))
            ->click();

        sleep(1);
        $windowHandlesAfter = $this->webDriver->getWindowHandles();
        $newWindowHandle = array_diff($windowHandlesAfter, $windowHandlesBefore);
        $this->webDriver->switchTo()->window(reset($newWindowHandle));

        $this->webDriver->findElement(WebDriverBy::name("Clients[phone]"))
            ->click()
            ->sendKeys(mt_rand(1000000000, 9999999999));
        $this->webDriver->findElement(WebDriverBy::name("Clients[last_name]"))
            ->click()
            ->sendKeys('фамилия');
        $this->webDriver->findElement(WebDriverBy::name("Clients[name]"))
            ->click()
            ->sendKeys('имя');

        $this->webDriver->executeScript("javascript:window.scrollBy(250,350)");

        $this->webDriver->findElement(WebDriverBy::name("Clients[middle_name]"))
            ->click()
            ->sendKeys('отчество');
        $this->webDriver->findElement(WebDriverBy::name("Clients[date_of_birth]"))
            ->click()
            ->sendKeys('11111111');
        // Паспортные данные
        $this->webDriver->findElement(WebDriverBy::name("Clients[passport_series]"))
            ->click()
            ->sendKeys('1111');
        $this->webDriver->findElement(WebDriverBy::name("Clients[passport_number]"))
            ->click()
            ->sendKeys('111111');
        $this->webDriver->findElement(WebDriverBy::name("Clients[division_code]"))
            ->click()
            ->sendKeys('111111');
        $this->webDriver->findElement(WebDriverBy::name("Clients[date_passport_issue]"))
            ->click()
            ->sendKeys('11111111');

        $this->webDriver->executeScript("javascript:window.scrollBy(250,350)");

        sleep(1);

        //KOSTIL
        $this->webDriver->executeScript("
        var checkboxes = document.getElementsByName('checkbox');
        checkboxes[0].checked = true;
        checkboxes[1].checked = true;
        ");

        print ($this->webDriver->getTitle());
        $this->assertEquals('Первый заем под 0%', $this->webDriver->getTitle());
    }
}