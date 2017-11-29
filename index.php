<?php
require __DIR__ . '/vendor/autoload.php';
ini_set('display_errors', 1);

/**
 * @param $data
 * @param $code
 */
function response($data, $code)
{
    http_response_code($code);

    if($data instanceof \TestJustCms\Models\Posts)
    {
        echo json_encode($data->jsonSerialize());
        die;
    }

    if(is_bool($data))
    {
        echo $data? json_encode(['error' => false, 'message' => 'Successful operation!']):
            json_encode(['error' => true, 'message' => 'Unsuccessful operation, contact support for more information.']);
        die;
    }

    if(is_array($data) && isset($data[0]) && $data[0] instanceof \TestJustCms\Models\Posts)
    {
        $response = [];

        foreach ($data as $post)
        {
            $response[] = [
                'id'    => $post->id,
                'title' =>  $post->title,
                'body' =>  $post->body,
                'path' =>  $post->path,
                'created_at' =>  $post->created_at,
                'updated_at' =>  $post->updated_at
            ];
        }

        echo json_encode($response);
        die;
    }

    echo json_encode($data);die;
}

require_once 'src/routes.php';