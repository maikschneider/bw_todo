<?php
declare(strict_types=1);

namespace Blueways\BwTodo\Tests\Functional\Controller;

use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Blueways\BwTodo\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ProfileControllerTest extends FunctionalTestCase
{

    use SiteBasedTestTrait;

    protected ?QueryBuilder $queryBuilder = null;

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

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_bwtodo_domain_model_profile');
        $connection->truncate('pages');
        $connection->truncate('tx_bwtodo_domain_model_profile');
        $connection->truncate('tx_bwtodo_domain_model_task');

        $this->queryBuilder = $connection->createQueryBuilder();
        $this->importDataSet(__DIR__ . '/../../Fixtures/pages.xml');
        $this->setUpFrontendRootPage(1, [
            'setup' => ['EXT:bw_todo/Configuration/TypoScript/setup.typoscript'],
            'constants' => ['EXT:bw_todo/Tests/Fixtures/constants.typoscript']
        ]);

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

        // test response and empty result set
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
        $this->assertEquals([], json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR));
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

        // test response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());

        // test result count
        $profiles = json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(3, $profiles);

        // test data types
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
        $this->assertJson((string)$response->getBody());
        $profile = json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);
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
        $postData = [
            'name' => 'Test-Todo'
        ];

        $request = (new InternalRequest())
            ->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Profile',
                'tx_bwtodo_api[action]' => 'index',
            ])
            ->withMethod('POST')
            ->withParsedBody($postData);

        // test response
        $response = $this->executeFrontendSubRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string)$response->getBody();
        $this->assertJson($body);

        // test response object
        $profile = json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        $this->assertIsObject($profile);
        $this->assertEquals('Test-Todo', $profile->name);
        $this->assertEquals(1, $profile->uid);

        // test database
        $profiles = $this->queryBuilder->select('*')
            ->from('tx_bwtodo_domain_model_profile')
            ->executeQuery()
            ->fetchAllAssociative();
        $this->assertCount(1, $profiles, 'Only one profile in database');
        $this->assertEquals('Test-Todo', $profiles[0]['name']);
    }

    /**
     * @TODO: PUT requests are not working with current testing-framework
     * @return void
     */
    public function testUpdate(): void
    {

    }

    public function testDelete(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_profile.xml');
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_task.xml');

        $request = (new InternalRequest())
            ->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Profile',
                'tx_bwtodo_api[action]' => 'detail',
                'tx_bwtodo_api[profile]' => 2,
            ])
            ->withMethod('DELETE');

        // test response
        $response = $this->executeFrontendSubRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());

        // test profiles in database
        $profiles = $this->queryBuilder->select('*')
            ->from('tx_bwtodo_domain_model_profile')
            ->executeQuery()
            ->fetchAllKeyValue();
        $this->assertCount(2, $profiles, 'Only two profiles left in database');
        $this->assertNotContains(2, array_keys($profiles), 'Profile with uid:2 no longer in database');

        // test cascade remove of tasks
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_bwtodo_domain_model_task');
        $tasks = $connection->createQueryBuilder()->select('*')
            ->from('tx_bwtodo_domain_model_task')
            ->executeQuery()
            ->fetchAllKeyValue();
        $this->assertCount(3, $tasks, 'Only 3 tasks left in database');
        $this->assertNotContains(4, array_keys($profiles), 'Task with uid:4 no longer in database');
        $this->assertNotContains(5, array_keys($profiles), 'Task with uid:5 no longer in database');
    }
}
