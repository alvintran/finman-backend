<?php namespace Nht\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Nht\Hocs\Posts\Post;

class PostTransformer extends TransformerAbstract
{
    public function transform(Post $post)
    {
        return [
            'id'      => $post->id,
            'title'   => $post->title,
            'body'    => $post->body,
            'created' => date('d/m/Y', strtotime($post->created_at)),
            'updated' => date('d/m/Y', strtotime($post->updated_at))
        ];
    }
}
