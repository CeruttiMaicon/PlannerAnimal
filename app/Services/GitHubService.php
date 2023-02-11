<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Model;

final class GitHubService extends Model
{
    /**
     * @codeCoverageIgnore
     *
     * @param  GuzzleClient  $client
     */
    public function __construct(GuzzleClient $client = null)
    {
        $this->client = $client ?? new GuzzleClient();

        $this->accessToken = config('services.github.access_token');

        if (! $this->accessToken) {
            throw new \Throwable('Variáveis de conexão do GitHub não declaradas');
        }
    }

    /**
     * @codeCoverageIgnore
     * Verifica se o usuário tem permissão para acessar o repositório.
     *
     * GitHub API Docs: https://docs.github.com/pt/rest/collaborators/collaborators?apiVersion=2022-11-28#check-if-a-user-is-a-repository-collaborator
     *
     * @param  string  $nickName
     * @return bool
     */
    public function verifyPermissionUser(string $nickName)
    {
        try {
            $response = $this->client->get(
                "https://api.github.com/repos/Zoren-Software/VoleiClub/collaborators/$nickName",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('GITHUB_ACCESS_TOKEN'),
                    ],
                ]
            );
            if ($response->getStatusCode() == 204) {
                return true;
            } elseif ($response->getStatusCode() == 404) {
                return false;
            } elseif ($response->getStatusCode() == 401) {
                throw new \Exception('Token de acesso inválido');
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
}
