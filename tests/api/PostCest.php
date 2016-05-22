<?php

class PostCest
{
    protected $endpoint = '/posts';
    protected $token = '';

    public function _before(ApiTester $I)
    {
        $I->haveRecord('users', ['email' => 'tester@gmail.com', 'password' => bcrypt('testing'), 'name' => 'Tester']);
        $I->sendPOST('/login', ['email' => 'tester@gmail.com', 'password' => 'testing']);
        $I->seeResponseCodeIs(200);
        $token = $I->grabDataFromResponseByJsonPath('$.token');
        $this->token = !isset($token[0]) ? : $token[0];
    }

    public function getAllPosts(ApiTester $I)
    {
        $id = (string) $this->havePost($I, ['title' => 'Game of Thrones']);
        $id2 = (string) $this->havePost($I, ['title' => 'Lord of the Rings']);

        $I->amBearerAuthenticated($this->token);
        $I->sendGET($this->endpoint);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->expect('both items are in response');
        $I->seeResponseContainsJson(['id' => $id, 'title' => 'Game of Thrones', 'body' => 'Body']);
        $I->seeResponseContainsJson(['id' => $id2, 'title' => 'Lord of the Rings', 'body' => 'Body']);
        $I->expect('both items are in root array');
        $I->seeResponseContainsJson([['id' => $id], ['id' => $id2]]);
    }

    public function getSinglePost(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);

        // Get a not exist post
        $I->sendGET($this->endpoint."/99999999");
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Post not found');

        $id = (string) $this->havePost($I, ['title' => 'Starwars']);
        $I->sendGET($this->endpoint."/$id");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['id' => $id, 'title' => 'Starwars']);
        $I->expect('there is no root array in response');
        $I->dontSeeResponseContainsJson([['id' => $id]]);
    }

    public function createPost(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);

        // Validation fail
        $I->sendPOST($this->endpoint, ['title' => '', 'body' => 'By Alvin']);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContains('The title field is required.');

        // Validation success
        $I->sendPOST($this->endpoint, ['title' => 'Game of Rings', 'body' => 'By George Tolkien']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['title' => 'Game of Rings']);
        $id = $I->grabDataFromResponseByJsonPath('$..id')[0];
        $I->seeRecord('posts', ['id' => $id, 'title' => 'Game of Rings']);
        $I->sendGET($this->endpoint."/$id");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['title' => 'Game of Rings']);
    }

    public function updatePost(ApiTester $I)
    {
        $id = (string) $this->havePost($I, ['title' => 'Game of Thrones']);

        $I->amBearerAuthenticated($this->token);
        $I->sendPUT($this->endpoint."/$id", ['title' => 'Lord of Thrones']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['title' => 'Lord of Thrones']);
        $I->seeRecord('posts', ['title' => 'Lord of Thrones']);
        $I->dontSeeRecord('posts', ['title' => 'Game of Thrones']);
    }

    public function deletePost(ApiTester $I)
    {
        $id = (string) $this->havePost($I, ['title' => 'Game of Thrones']);

        $I->amBearerAuthenticated($this->token);
        $I->sendDELETE($this->endpoint."/$id");
        $I->seeResponseCodeIs(204);
        $I->dontSeeRecord('posts', ['id' => $id]);
    }

    private function havePost(ApiTester $I, $data = [])
    {
       $data = array_merge([
               'title' => 'Game of Thrones',
               'body' => 'Body',
               'created_at' => new \DateTime(),
               'updated_at' => new  \DateTime(),
       ], $data);
       return $I->haveRecord('posts', $data);
    }
}
