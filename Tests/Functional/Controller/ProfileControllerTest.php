<?php

namespace Blueways\BwTodo\Tests\Functional\Controller;

use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Blueways\BwTodo\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ProfileControllerTest extends FunctionalTestCase
{

    use SiteBasedTestTrait;

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8']
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/bw_todo'
    ];

    protected array $configurationToUseInTestInstance = [
        'DB' => [
            'Connections' => [
                'Default' => [
                    'dbname' => 'testing',
                    'host' => 'db',
                    'password' => 'root',
                    'port' => '3306',
                    'user' => 'root',
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;
        $this->profileRepository = $this->createMock(ProfileRepository::class);

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages');
        $connection->truncate('pages');
        $connection->truncate('tx_bwtodo_domain_model_profile');
        $connection->truncate('tx_bwtodo_domain_model_task');
        $this->importDataSet(__DIR__ . '/../../Fixtures/pages.xml');
        $this->setUpFrontendRootPage(1, ['setup' => ['EXT:bw_todo/Configuration/TypoScript/setup.typoscript']]);

        $siteConf = $this->buildSiteConfiguration(1, '/');
        $siteConf['imports'] = [
            0 => ['resource' => 'EXT:bw_todo/Configuration/Routing/Api.yaml']
        ];

        $this->writeSiteConfiguration(
            'main',
            $siteConf,
            [
                $this->buildDefaultLanguageConfiguration('EN', '/')
            ]
        );
    }

    public function testEmptyList(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Profile',
                'tx_bwtodo_api[action]' => 'index',
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
        $this->assertEquals([], json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    public function testList(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_profile.xml');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Profile',
                'tx_bwtodo_api[action]' => 'index',
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
        $profiles = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(3, $profiles);
        $this->assertIsObject($profiles[0]);
        $this->assertIsInt($profiles[0]->uid);
        $this->assertIsString($profiles[0]->name);
        $this->assertIsArray($profiles[0]->tasks);
    }

    public function testShow(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_profile.xml');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Profile',
                'tx_bwtodo_api[action]' => 'detail',
                'tx_bwtodo_api[profile]' => 2,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
        $profile = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        $this->assertIsObject($profile);

        $expectedProfile = [
            'uid' => 2,
            'name' => 'Garden Todos',
            'tasks' => []
        ];
        $this->assertEquals($expectedProfile, (array)$profile);
    }

    public function testCreate(): void
    {
        $body = [
            'name' => 'Test-Todo'
        ];

        $data = array('name' => 'Check1', 'bla' => 'foo');
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $stream = new \TYPO3\CMS\Core\Http\Stream($context);

        $body = new Stream('php://temp', 'wb+');
        $body->write('fwfe=test');
        $body->rewind();

        $stream = (new StreamFactory())->createStream(http_build_query($data));

        \TYPO3\CMS\Core\Utility\DebugUtility::debug($options, 'Debug: ' . __FILE__ . ' in Line: ' . __LINE__);

        $request = (new InternalRequest())
            ->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Profile',
                'tx_bwtodo_api[action]' => 'index',
            ])
            ->withMethod('POST')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        $response = $this->executeFrontendSubRequest($request);
    }
}
