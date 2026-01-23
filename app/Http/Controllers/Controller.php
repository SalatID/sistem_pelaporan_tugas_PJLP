<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function createJwt($payloadData)
    {
        /*
         Fungsi yang digunakan untuk generate JWT Token, dari credential menjadi JWT Token
         */
        $privateKey = file_get_contents(dirname(__FILE__, 4).env('JWKS_JWT_PRIVATE_PATH'));

        if (false === $privateKey) {
            return false;
        }

        $json = file_get_contents(dirname(__FILE__, 4).env('JWKS_PATH'));
        
        if (false === $json) {
            return false;
        }
        
        $jwks = json_decode($json, true);
        // Generate a token
        $jwt = JWT::encode($payloadData, $privateKey, 'RS256', $jwks['keys'][0]['kid']);

        return $jwt;
    }

    public static function parseJwt($jwt)
    {
        try{
        /*
            Fungsi yang digunakan untuk merubah JWT Token menjadi Credential
            */
            $supportedAlgorithm = [
                'ES384','ES256', 'HS256', 'HS384', 'HS512', 'RS256', 'RS384','RS512'
            ];
            $json = file_get_contents(dirname(__FILE__, 4).env('JWKS_JWT_PUBLIC_PATH'));

            if (false === $json) {
                return false;
            }

            $jwks = self::getPublicKeyFromCertificate($json);
            $decode = JWT::decode($jwt, new Key($jwks, 'RS256'));
            return json_decode(json_encode([
                'error'=>false,
                'message'=>"Decode Success",
                'data'=>$decode
            ]));
        } catch (\Firebase\JWT\ExpiredException $e) {
            return json_decode(json_encode([
                'error'=>true,
                'message'=>"Token Expired"
            ]));
            
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return json_decode(json_encode([
                'error'=>true,
                'message'=>"Invalid Signature"
            ]));
            
        } catch (\UnexpectedValueException $e) {
            // Tangani error jika terjadi kesalahan lainnya
            return json_decode(json_encode([
                'error'=>true,
                'message'=>$e->getMessage()
            ]));
        }

    }
    public static function getPublicKeyFromCertificate($certificatePath) {
        // $certificate = file_get_contents($certificatePath);
        
        // Mendapatkan kunci publik dari file certificate.pem
        $publicKey = openssl_pkey_get_public($certificatePath);
        
        // Konversi kunci publik ke format yang dapat digunakan oleh library JWT
        $keyDetails = openssl_pkey_get_details($publicKey);
        $publicKeyString = $keyDetails['key'];
        
        // Mengembalikan kunci publik dalam format yang tepat
        return $publicKeyString;
    }

    public static function list_menu()
    {
        $menu = [
            [
                "id"=>"1",
                "icon"=>"fas fa-database",
                "name"=>"Master Data",
                "src"=>"#",
                "permision"=>["jabatan_list","lokasi_list","kategori_list"],
                "children"=>[
                    [
                        "icon"=>"fas fa-id-badge",
                        "name"=>"Jabatan",
                        "permision"=>["jabatan_list"],
                        "src"=>"jabatan.index"
                    ],
                    [
                        "icon"=>"fas fa-map-marker-alt",
                        "name"=>"Lokasi",
                        "permision"=>["lokasi_list"],
                        "src"=>"lokasi.index"
                    ],
                    [
                        "icon"=>"fas fa-tags",
                        "name"=>"Kategori",
                        "permision"=>["kategori_list"],
                        "src"=>"kategori.index"
                    ],
                ],
            ],
            [
                "id"=>"2",
                "icon"=>"fas fa-tasks",
                "name"=>"Operasional",
                "src"=>"#",
                "permision"=>["tugas_list"],
                "children"=>[
                    [
                        "icon"=>"fas fa-clipboard-list",
                        "name"=>"Tugas/Laporan",
                        "permision"=>["tugas_list"],
                        "src"=>"tugas.index"
                    ],
                ],
            ],
            [
                "id"=>"3",
                "icon"=>"fas fa-users",
                "name"=>"Manajemen User",
                "src"=>"#",
                "permision"=>["user_list","role_list","permission_list"],
                "children"=>[
                    [
                        "icon"=>"fas fa-user",
                        "name"=>"User List",
                        "permision"=>["user_list"],
                        "src"=>"users.index"
                    ],
                    [
                        "icon"=>"fas fa-lock",
                        "name"=>"Role",
                        "permision"=>["role_list"],
                        "src"=>"roles.index"
                    ],
                    [
                        "icon"=>"fas fa-user-lock",
                        "name"=>"Permission",
                        "permision"=>["permission_list"],
                        "src"=>"permissions.index"
                    ],
                ],
            ],
        ];
        return $menu;
    }

    public static function label_color($var){
        switch ($var) {
            case 'edit':
                $color = "primary";
                break;
            case 'delete':
                $color = "danger";
                break;
            case 'create':
                $color = "success";
                break;
            case 'show' || 'list':
                $color = "info";
                break;
            
            default:
                $color = "success";
                break;
            }
            return $color;
    }
    public function ensurePengawas(): void
    {
        $user = auth()->user();
        if ($user->hasRole('super-admin')) return;
        if (!$user->isPengawas()) abort(403);
    }

    public function ensureAdmin(): void
    {
        $user = auth()->user();
        if ($user->hasRole('super-admin')) return;
        if (!($u->isPengawas() || $u->isKordinator())) abort(403);
    }
}
