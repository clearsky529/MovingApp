<?php
/**
 * Created by Dineish Sailor
 * User: Dinesh
 * Email: dinesh.sailor@varshaaweblabs.com
 * Date: 11/20/2020
 * Time: 2:21 PM
 */

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestAnalytics
{
    protected $startTime;

    public function handle($request, Closure $next)
    {
        if(env('APP_DEBUG')) {
            $logFilename = public_path() . '/response-log.html';
            //file_put_contents($logFilename, '');
            //file_put_contents($logFilename, "\n " . microtime(true), FILE_APPEND);

            $request->startTime = LARAVEL_START;
            file_put_contents($logFilename, "\n<h3> New request starts from here</h3>", FILE_APPEND);
            file_put_contents($logFilename, "\n<h2>" . $request->fullUrl() . '</h2>', FILE_APPEND);
            // file_put_contents($logFilename, "\n<h4>Request start time: " . $request->startTime . '</h4>', FILE_APPEND);
            file_put_contents($logFilename, "\n<h3>Header:</h3><div style='overflow: scroll;border:1px dashed #ddd4b0;height: 500px;width:60%'><pre>" . print_r($request->header(), true) . '</pre></div>', FILE_APPEND);
            file_put_contents($logFilename, "\n<h3>Header JSON:</h3><pre>" . json_encode($request->header()) . '</pre>', FILE_APPEND);
            file_put_contents($logFilename, "\n<h3>Params:</h3><div style='overflow: scroll;border:1px dashed #ddd4b0;height: 500px;width:60%'><pre>" . print_r($request->all(), true) . '</pre></div>', FILE_APPEND);
            file_put_contents($logFilename, "\n<h3>Params JSON:</h3><pre>" . json_encode($request->all()) . '</pre>', FILE_APPEND);

            self::enableQueryLog();
        }
        return $next($request);
    }

    public function terminate($request, $response): void
    {
        if(env('APP_DEBUG')) {
            $logFilename = public_path() . '/response-log.html';
            if (file_exists($logFilename)) {
                $intSizeLimit = 1024 * 1024 * 2;
                $intSize = filesize($logFilename);

                if ($intSize > $intSizeLimit) {
                    file_put_contents($logFilename, '');
                }
            }
            //file_put_contents($logFilename, "\n<h3>Response:</h3><pre>" . \GuzzleHttp\json_encode($response->getData()) . '</pre>', FILE_APPEND);

            $responseTime = round(microtime(true) - $request->startTime, 2);
            $arrSql = self::getRealQueryLog();
            file_put_contents($logFilename, "\n<h3>Request to Response Time log:</h3><pre>Request start time: " . ($request->startTime . ' Micro Seconds | Request end time: ' . microtime(true) . ' Micro Seconds <br><br> Total Response Time: <span style="color: red;font-weight: bold;font-size: 1rem">' . $responseTime. '</span>') . ' Seconds | Total SQL Time: <span style="color: red;font-weight: bold;font-size: 1rem">' . $arrSql['total_time']. '</span>'. ' Seconds</pre>', FILE_APPEND);
            file_put_contents($logFilename, "\n<h3>SQL queries run during request to response:</h3><div style='overflow: scroll;border:1px dashed #ddd4b0;height: 500px;width:60%'><pre>" . print_r($arrSql, true) . '</pre></div>', FILE_APPEND);
            file_put_contents($logFilename, "\n<h3> Previous request ends here</h3><br/><br/><br/>", FILE_APPEND);
            //file_put_contents($logFilename, "\n<h3>Response Time:</h3>" . $responseTime . ' Seconds', FILE_APPEND);
        }
    }

    public static function enableQueryLog()
    {
        return DB::enableQueryLog();
    }

    public static function getRealQueryLog(): array
    {
        $arrQueryLog = DB::getQueryLog();
        $arrHighQueryLog = [];
        $total_time = 0;

        foreach ($arrQueryLog as $key => $query) {
            foreach ($query['bindings'] as $i => $bind) {
                $arrQueryLog[$key]['bindings'][$i] = "'" . ($bind instanceof DateTime ? $bind->format('d/m/Y') : (string)$bind) . "'";
            }
            $arrQueryLog[$key]['real_query'] = Str::replaceArray('?', $arrQueryLog[$key]['bindings'], $query['query']);
            $arrQueryLog[$key]['time'] /= 1000; // microtime https://github.com/laravel/framework/blob/da5bdc94574d796b136d607365619506ca67ac50/src/Illuminate/Database/Connection.php#L592

            unset($arrQueryLog[$key]['query'], $arrQueryLog[$key]['bindings']);

            if ($arrQueryLog[$key]['time'] > .1) {
                $arrHighQueryLog[] = $arrQueryLog[$key];
            }

            $total_time += $arrQueryLog[$key]['time'];
        }

        //dd($arrQueryLog);

        return ['all' => $arrQueryLog, 'high' => $arrHighQueryLog, 'total_time' => $total_time. ' Seconds'];
    }
}
