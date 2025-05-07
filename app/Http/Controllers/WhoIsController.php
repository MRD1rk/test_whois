<?php

namespace App\Http\Controllers;

use App\Http\Requests\LookupWhoIsRequest;

class WhoIsController extends Controller
{
    public function form()
    {
        return view('whois.form');
    }

    public function lookup(LookupWhoIsRequest $request)
    {
        $tldServers = config('whois.servers');  // краще винести в config/whois.php

        $domain = $request->input('domain');
        $parts  = explode('.', $domain);
        $tld    = strtolower(array_pop($parts));
        $server = $tldServers[$tld] ?? null;

        if (! $server) {
            return response()->json([
                'error' => "Невідомий WHOIS-сервер для TLD «{$tld}»."
            ], 400);
        }

        try {
            $fp = fsockopen($server, 43, $errno, $errstr, 10);
            if (! $fp) {
                throw new \RuntimeException("Не вдалося підключитися: [{$errno}] {$errstr}");
            }

            $query = "{$domain}\r\n";

            fwrite($fp, $query);

            $response = '';
            while (! feof($fp)) {
                $response .= fgets($fp, 128);
            }
            fclose($fp);

            return response()->json([
                'domain' => $domain,
                'server' => $server,
                'whois'    => $response,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 503);
        }
    }
}
