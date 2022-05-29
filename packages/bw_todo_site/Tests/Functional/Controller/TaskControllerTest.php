<?php

namespace Blueways\BwTodoSite\Tests\Functional\Controller;

use Blueways\BwTodoSite\Domain\Repository\ProfileRepository;
use Blueways\BwTodoSite\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TaskControllerTest extends FunctionalTestCase
{

    use SiteBasedTestTrait;

    protected ?QueryBuilder $queryBuilder = null;

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8']
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/bw_todo'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_bwtodosite_domain_model_task');
        $connection->truncate('pages');
        $connection->truncate('tx_bwtodosite_domain_model_profile');
        $connection->truncate('tx_bwtodosite_domain_model_task');

        $this->queryBuilder = $connection->createQueryBuilder();
        $this->importDataSet(__DIR__ . '/../../Fixtures/pages.xml');
        $this->setUpFrontendRootPage(1, [
            'setup' => ['EXT:bw_todo_site/Configuration/TypoScript/setup.typoscript'],
            'constants' => ['EXT:bw_todo_site/Tests/Fixtures/constants.typoscript']
        ]);

        $siteConf = $this->buildSiteConfiguration(1, '/');
        $siteConf['imports'] = [
            0 => ['resource' => 'EXT:bw_todo_site/Configuration/Routing/Api.yaml']
        ];

        $this->writeSiteConfiguration(
            'main',
            $siteConf,
            [
                $this->buildDefaultLanguageConfiguration('EN', '/')
            ]
        );
    }

    public function testDelete(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodosite_domain_model_task.xml');

        $request = (new InternalRequest())
            ->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodosite_api[controller]' => 'Task',
                'tx_bwtodosite_api[action]' => 'delete',
                'tx_bwtodosite_api[task]' => 3,
            ])
            ->withMethod('DELETE');
        $response = $this->executeFrontendSubRequest($request);

        $tasks = $this->queryBuilder->select('*')
            ->from('tx_bwtodosite_domain_model_task')
            ->executeQuery()
            ->fetchAllKeyValue();

        // test response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());

        // test database result
        $this->assertCount(5, $tasks, 'Only 5 tasks left in database');
        $this->assertNotContains(3, array_keys($tasks), 'Task with uid:3 no longer in database');
    }

    public function testCreate(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodosite_domain_model_profile.xml');

        $postData = [
            'title' => 'Test-Task',
            'description' => 'Lorem ipsum',
            'dueDate' => '04.03.2022-07:09'
        ];

        $request = (new InternalRequest())
            ->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodosite_api[controller]' => 'Task',
                'tx_bwtodosite_api[action]' => 'create',
                'tx_bwtodosite_api[profile]' => 1,
            ])
            ->withMethod('POST')
            ->withParsedBody($postData);

        // test response
        $response = $this->executeFrontendSubRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody();
        $this->assertJson($body);

        // test response object
        $task = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        $this->assertIsObject($task);
        $this->assertEquals('Test-Task', $task->title);
        $this->assertEquals('Lorem ipsum', $task->description);
        $this->assertEquals(1, $task->uid);
        $this->assertEquals('2022-03-04T07:09:00+00:00', $task->dueDate);

        // test database
        $tasks = $this->queryBuilder->select('*')
            ->from('tx_bwtodosite_domain_model_task')
            ->executeQuery()
            ->fetchAllAssociative();
        $this->assertCount(1, $tasks, 'Only one task in database');
        $this->assertEquals('Test-Task', $tasks[0]['title']);
        $this->assertEquals('Lorem ipsum', $tasks[0]['description']);
        $this->assertEquals(1, $tasks[0]['profile']);
        $this->assertEquals(1646377740, $tasks[0]['due_date']);
    }
}
