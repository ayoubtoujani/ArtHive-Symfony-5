<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookShareController extends AbstractController
{
    /**
     * @Route("/share-to-facebook", name="share_to_facebook")
     */
    public function shareToFacebook(): Response
    {
        // Your Facebook App ID and App Secret
        $appId = '1848760258882344';
        $appSecret = 'c18896181e3d4c9d107e4dd79c5963b6';

        // Generate an App Access Token
        $accessTokenUrl = 'https://graph.facebook.com/oauth/access_token';
        $params = [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'grant_type' => 'client_credentials',
        ];

        $response = file_get_contents($accessTokenUrl . '?' . http_build_query($params));
        $responseData = json_decode($response, true);

        // Extract the App Access Token
        if (isset($responseData['access_token'])) {
            $appAccessToken = $responseData['access_token'];

            // Content to share
            $pageId = '1564305041087890'; // Your Facebook user ID
            $message = 'Check out this content from our website!';
            $link = 'http://127.0.0.1:8000/showComments/208'; // URL of the content to be shared
            $picture = 'http://example.com/image.jpg'; // URL of an optional image

            // Prepare data for sharing
            $postData = [
                'message' => $message,
                'link' => $link,
                'picture' => $picture,
                'access_token' => $appAccessToken,
            ];

            $graphUrl = 'https://graph.facebook.com/' . $pageId . '/feed';

            // Make a POST request to share content
            $ch = curl_init($graphUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Handle the response accordingly
            if ($response === false) {
                // Error occurred
                return new Response('Error sharing content to Facebook');
            } else {
                // Content shared successfully
                return new Response('Content shared successfully on Facebook');
            }
        } else {
            // Failed to generate App Access Token
            return new Response('Failed to generate App Access Token');
        }
    }
}
