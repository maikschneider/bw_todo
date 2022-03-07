<?php
declare(strict_types=1);

namespace Blueways\BwTodo\Tests\Functional\Controller;

use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Blueways\BwTodo\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\CMS\Core\Core\Environment;
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

        // prepare database @TODO: there must be an auto-truncate or reconstruction of test database..
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_bwtodo_domain_model_profile');
        $connection->truncate('pages');
        $connection->truncate('tx_bwtodo_domain_model_profile');
        $connection->truncate('tx_bwtodo_domain_model_task');

        // set up query builder
        $this->queryBuilder = $connection->createQueryBuilder();
        $this->importDataSet(__DIR__ . '/../../Fixtures/pages.xml');
        $this->setUpFrontendRootPage(1, [
            'setup' => ['EXT:bw_todo/Configuration/TypoScript/setup.typoscript'],
            'constants' => ['EXT:bw_todo/Tests/Fixtures/constants.typoscript']
        ]);

        // set up site config
        $file = 'EXT:bw_todo/config/sites/main/config.yaml';
        $path = Environment::getConfigPath() . '/sites/main/';
        $target = $path . 'config.yaml';
        if (!file_exists($target)) {
            GeneralUtility::mkdir_deep($path);
            if (!file_exists($file)) {
                $file = GeneralUtility::getFileAbsFileName($file);
            }
            $fileContent = file_get_contents($file);
            GeneralUtility::writeFile($target, $fileContent);
        }
    }

    public function testEmptyList(): void
    {
        $request = new InternalRequest('https://bw-todo.ddev.site/profile.json');
        $response = $this->executeFrontendSubRequest($request, null, true);

        // test response and empty result set
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
        $this->assertEquals([], json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    public function testList(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_profile.xml');

        $request = new InternalRequest('https://bw-todo.ddev.site/profile.json');
        $response = $this->executeFrontendSubRequest($request, null, true);

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

        $request = new InternalRequest('https://bw-todo.ddev.site/profile/2.json');
        $response = $this->executeFrontendSubRequest($request, null, true);

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
        $request = new InternalRequest('https://bw-todo.ddev.site/profile.json');
        $request = $request->withMethod('POST');
        $request = $request->withParsedBody(['name' => 'Test-Todo']);

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

    public function testUnknownMethod()
    {
        $request = new InternalRequest('https://bw-todo.ddev.site/profile.json');
        $request = $request->withMethod('PUT');

        $response = $this->executeFrontendSubRequest($request);

        $bo = (string)$response->getBody();

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }

    public function testUpdate(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_profile.xml');

        $request = new InternalRequest('https://bw-todo.ddev.site/profile/2.json');
        $request = $request->withMethod('PATCH');

        // test invalid response
        $invalidRequest = $request->withParsedBody(['name' => str_repeat('X', 300)]);
        $response = $this->executeFrontendSubRequest($invalidRequest);
        //$this->assertEquals(500, $response->getStatusCode());

        // test valid response
        $validRequest = $request->withParsedBody(['name' => 'NewTitle']);
        $response = $this->executeFrontendSubRequest($validRequest);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());

        // check updated profile in database
        $profiles = $this->queryBuilder->select('*')
            ->from('tx_bwtodo_domain_model_profile')
            ->executeQuery()
            ->fetchAllAssociative();
        $this->assertEquals('NewTitle', $profiles[1]['name'], 'Profile with uid:2 has updated title');
    }

    public function testDelete(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_profile.xml');
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_task.xml');

        $request = new InternalRequest('https://bw-todo.ddev.site/profile/2.json');
        $request = $request->withMethod('DELETE');

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
