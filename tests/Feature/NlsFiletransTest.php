<?php

namespace AlibabaCloud\Tests\Feature;

use PHPUnit\Framework\TestCase;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\NlsFiletrans\NlsFiletrans;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\NlsFiletrans\V20180817\SubmitTask;

/**
 * Class NlsFiletransTest
 *
 * @package   AlibabaCloud\Tests\Feature
 */
class NlsFiletransTest extends TestCase
{
    /**
     * @throws ClientException
     */
    public function setUp()
    {
        parent::setUp();

        AlibabaCloud::accessKeyClient(
            \getenv('ACCESS_KEY_ID'),
            \getenv('ACCESS_KEY_SECRET')
        )->regionId('cn-shanghai')->asDefaultClient();
    }

    public function testVersionResolve()
    {
        $request1 = AlibabaCloud::nlsFiletrans()
                                ->v20180817()
                                ->submitTask()
                                ->connectTimeout(20)
                                ->timeout(25);

        $request2 = NlsFiletrans::v20180817()
                                ->submitTask()
                                ->connectTimeout(20)
                                ->timeout(25);

        self::assertInstanceOf(SubmitTask::class, $request1);
        self::assertInstanceOf(SubmitTask::class, $request2);
        self::assertEquals($request1, $request2);
    }

    /**
     * @return string
     * @throws ClientException
     * @throws ServerException
     */
    public function testSubmitTask()
    {
        $result = NlsFiletrans::v20180817()
                              ->submitTask()
                              ->host('filetrans.cn-shanghai.aliyuncs.com')
                              ->withTask(json_encode([
                                                         'file_link' => 'https://aliyun-nls.oss-cn-hangzhou.aliyuncs.com/asr/fileASR/examples/nls-sample-16k.wav',
                                                     ]))
                              ->withDebug('true')
                              ->connectTimeout(20)
                              ->timeout(25)
                              ->request();

        self::assertEquals('REQUEST_EMPTY_APPKEY_VALUE', $result['StatusText']);
    }

    /**
     * @throws ClientException
     * @throws ServerException
     */
    public function testGetTaskResult()
    {
        $result = NlsFiletrans::v20180817()
                              ->getTaskResult()
                              ->host('filetrans.cn-shanghai.aliyuncs.com')
                              ->withTaskId('FILE_TRANS_TASK_EXPIRED')
                              ->withDebug('true')
                              ->connectTimeout(20)
                              ->timeout(25)
                              ->request();

        self::assertEquals('REQUEST_INVALID_TASK_ID', $result['StatusText']);
    }

    /**
     * @throws ServerException
     * @throws ClientException
     */
    public function testSubmitTaskWithClientMethod()
    {
        $result = AlibabaCloud::rpc()
                              ->product('nls-cloud-meta')
                              ->version('2018-08-17')
                              ->method('POST')
                              ->action('SubmitTask')
                              ->host('filetrans.cn-shanghai.aliyuncs.com')
                              ->options([
                                            'query' => [
                                                'Task'  => json_encode([
                                                                           'app_key_no' => 'app_key',
                                                                           'file_link'  => 'https://aliyun-nls.oss-cn-hangzhou.aliyuncs.com/asr/fileASR/examples/nls-sample-16k.wav',
                                                                       ]),
                                                'Debug' => 'true',
                                            ],
                                        ])
                              ->connectTimeout(35)
                              ->timeout(40)
                              ->request();

        self::assertEquals('REQUEST_EMPTY_APPKEY_VALUE', $result['StatusText']);
    }
}
