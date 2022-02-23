<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;

class CommentControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_create_comment()
    {
        $comment = [
            'author_name' => 'Test name',
            'text' => __METHOD__,
        ];

        $response = $this->post(route('comments.store'), $comment);
        $response->assertStatus(201);
        $response->assertJson([
            'id'            => $response->json('id'),
            'author_name'   => $comment['author_name'],
            'text'          => $comment['text'],
            'parent_id'     => 0,
            'created_at'    => $response->json('created_at'),
            'updated'       => false,
            'children'       => [],
        ]);

        return $response->json();
    }


    /**
     * @depends test_can_create_comment
     */
    public function test_can_reply_comment(array $createdComment)
    {
        $comment = [
            'text' => __METHOD__ . ' (Reply)',
            'parent_id' => $createdComment['id']
        ];

        $response = $this->post(route('comments.store'), $comment);
        $response->assertCreated();
        $response->assertJson([
            'id'            => $response->json('id'),
            'author_name'   => null,
            'text'          => $comment['text'],
            'parent_id'     => $createdComment['id'],
            'created_at'    => $response->json('created_at'),
            'updated'       => false,
            'children'       => [],
        ]);

        return $response->json('id');
    }

    /**
     * @depends test_can_reply_comment
     */
    public function test_can_reply_to_child_comment(int $parentCommentId)
    {
        $comment = [
            'text' => __METHOD__,
            'parent_id' => $parentCommentId
        ];

        $response = $this->post(route('comments.store'), $comment);
        $response->assertCreated();
        $response->assertJsonStructure([
            'id'
            ,'author_name'
            ,'text'
            ,'parent_id'
            ,'created_at'
            ,'updated'
            ,'children'
        ]);
    }

    /**
     * @depends test_can_create_comment
     */
    public function test_can_destroy_comment(array $comment)
    {
        $response = $this->json('POST', route('comments.destroy', $comment['id']), ['_method' => 'DELETE']);
        $response->assertNoContent();

        return $comment['id'];
    }

    /**
     * @depends test_can_destroy_comment
     */
    public function test_cannot_destroy_nonexistent_comment($nonexistentCommentId)
    {
        $response = $this->post(route('comments.destroy', $nonexistentCommentId), ['_method' => 'DELETE']);
        $response->assertStatus(404);
    }

    /**
     * @depends test_can_destroy_comment
     */
    public function test_cannot_reply_to_nonexistent_comment($nonexistentParentCommentId)
    {
        $comment = [
            'text' => __METHOD__,
            'parent_id' => $nonexistentParentCommentId
        ];

        $response = $this->json('POST', route('comments.store'), $comment);
        $response->assertUnprocessable();
        $response->assertJsonFragment([
            'message' => "The selected parent id is invalid."
        ]);
    }

    public function test_can_update_comment()
    {
        $comment = Comment::create(['text' => __METHOD__]);
        $updatedComment = [
            'text' => __METHOD__ . ' (Updated)',
            '_method' => 'PUT'
        ];

        $response = $this->json('POST', route('comments.update', $comment['id']), $updatedComment);
        $response->assertStatus(200);
    }

    /**
     * @depends test_can_destroy_comment
     */
    public function test_cannot_update_nonexistent_comment($nonexistentCommentId)
    {
        $updatedComment = [
            'text' => __METHOD__ . ' (Updated)',
            '_method' => 'PUT'
        ];

        $response = $this->json('POST', route('comments.update', $nonexistentCommentId), $updatedComment);
        $response->assertNotFound();
    }

    public function test_cannot_create_comment_with_empty_text()
    {
        $comment = [
            'author_name' => 'Test name',
            'text' => '',
        ];

        $response = $this->json('POST', route('comments.store'), $comment);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => "The text field is required."
        ]);
    }

    public function test_cannot_create_comment_with_too_long_author_name()
    {
        $comment = [
            'author_name' => str_repeat('1', 51),
            'text' => '123',
        ];

        $response = $this->json('POST', route('comments.store'), $comment);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => "The author name must not be greater than 50 characters."
        ]);
    }
}
