<?php

namespace Blueways\BwTodo\Tests\Functional\Controller;

use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Blueways\BwTodo\Tests\Functional\SiteHandling\SiteBasedTestTrait;
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

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_bwtodo_domain_model_task');
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

    public function testDelete(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bwtodo_domain_model_task.xml');

        $request = (new InternalRequest())
            ->withQueryParameters([
                'id' => 1,
                'type' => '2927392',
                'tx_bwtodo_api[controller]' => 'Task',
                'tx_bwtodo_api[action]' => 'delete',
                'tx_bwtodo_api[task]' => 3,
            ])
            ->withMethod('DELETE');
        $response = $this->executeFrontendSubRequest($request);

        $tasks = $this->queryBuilder->select('*')
            ->from('tx_bwtodo_domain_model_task')
            ->executeQuery()
            ->fetchAllKeyValue();

        // test response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());

        // test database result
        $this->assertCount(5, $tasks, 'Only 5 tasks left in database');
        $this->assertNotContains(3, array_keys($tasks), 'Task with uid:3 no longer in database');
    }

    /**
     * @TODO: Since POST requests are not working with current testing framework
     * @return void
     */
    public function testCreate(): void
    {

    }
}
