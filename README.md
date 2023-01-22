# API Reference

## POST

### Get all posts

```http
  GET /api/post/
```

### Paginate post

```http
  GET /api/post?from=0&offset=5
```

### Create a new post

```http
  POST /api/post/

  Content-Type: multipart/form-data
```

| Parameter  | Type        | Description                                                      |
|:-----------|:------------|:-----------------------------------------------------------------|
| `title`    | `string`    | **Required**.  The title of the post **Length** min:3 max:100    |
| `content`  | `string`    | **Required**.  The content of the post **Length** min:3 max:3000 |
| `media`    | `mimes`     | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                       |
| `tags`     | `array:int` | **Optional**.  The tags(id) must be exists                       |
| `courseId` | `int`       | **Required**. Must be exists                                     |

### Update a post

```http
  POST /api/post/{id}

  Content-Type: multipart/form-data
```

| Parameter | Type        | Description                                                      |
|:----------|:------------|:-----------------------------------------------------------------|
| `title`   | `string`    | **Optional**.  The title of the post **Length** min:3 max:100    |
| `content` | `string`    | **Optional**.  The content of the post **Length** min:3 max:3000 |
| `media`   | `mimes`     | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                       |
| `tags`    | `array:int` | **Optional**.  The tags(id) must be exists                       |

### Show a post

```http
  GET /api/post/{id}
```

### Delete a post

```http
  DELETE /api/post/{id}
```

### Post Likes

```http
  PUT /api/post-like/

  Content-Type: appliation/json
```

| Parameter | Type  | Description                               |
|:----------|:------|:------------------------------------------|
| `postId`  | `int` | **Required**. The post(id) must be exists |

## QUESTION

### Get all questions

```http
  GET /api/question/
```

### Paginate question

```http
  GET /api/question?from=0&offset=5
```

### Create a new question

```http
  POST /api/question/

  Content-Type: multipart/form-data
```

| Parameter  | Type        | Description                                                          |
|:-----------|:------------|:---------------------------------------------------------------------|
| `title`    | `string`    | **Required**.  The title of the question **Length** min:3 max:100    |
| `content`  | `string`    | **Required**.  The content of the question **Length** min:3 max:3000 |
| `media`    | `mimes`     | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                           |
| `tags`     | `array:int` | **Optional**.  The tags(id) must be exists                           |
| `courseId` | `int`       | **Required**. Must be exists                                         |

### Update a question you can update question 5 minutes after adding

```http
  POST /api/question/{id}

  Content-Type: multipart/form-data
```

| Parameter | Type        | Description                                                          |
|:----------|:------------|:---------------------------------------------------------------------|
| `title`   | `string`    | **Optional**.  The title of the question **Length** min:3 max:100    |
| `content` | `string`    | **Optional**.  The content of the question **Length** min:3 max:3000 |
| `media`   | `mimes`     | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                           |
| `tags`    | `array:int` | **Optional**.  The tags(id) must be exists                           |

### Get question comments

```http
  GET /api/question/{id}/comments
```

### Paginate question comments

```http
  GET /api/question/{id}/comments?from=0&offset=5
```

### Show a question

```http
  GET /api/question/{id}
```

### Delete a question

```http
  DELETE /api/question/{id}
```

### Question Likes

```http
  PUT /api/question-like/

  Content-Type: appliation/json
```

| Parameter    | Type  | Description                                   |
|:-------------|:------|:----------------------------------------------|
| `questionId` | `int` | **Required**. The question(id) must be exists |

## COMMENTS

### Create a new comment

```http
  POST /api/comment/

  Content-Type: multipart/form-data
```

| Parameter    | Type     | Description                                                         |
|:-------------|:---------|:--------------------------------------------------------------------|
| `content`    | `string` | **Required**.  The content of the comment **Length** min:3 max:3000 |
| `questionId` | `int`    | **Required**. Must be exists                                        |
| `media`      | `mimes`  | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                          |
| `parentId`   | `int`    | **Optional**. Must be exists                                        |

### Update a comment you can update comment 5 minutes after adding

```http
  POST /api/comment/{id}

  Content-Type: multipart/form-data
```

| Parameter | Type     | Description                                                         |
|:----------|:---------|:--------------------------------------------------------------------|
| `content` | `string` | **Optional**.  The content of the comment **Length** min:3 max:3000 |
| `media`   | `mimes`  | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                          |

### Delete a comment

```http
  DELETE /api/comment/{id}
```

### Comment Rates

```http
  PUT /api/comment-rate/

  Content-Type: appliation/json
```

| Parameter   | Type  | Description                                                           |
|:------------|:------|:----------------------------------------------------------------------|
| `commentId` | `int` | **Required**. The comment(id) must be exists                          |
| `value`     | `int` | **Required**. The value must be similar to one of these enums(-1,0,1) |

## TAGS *In progress..*

### Get all tags

```http
  GET /api/tag/
```

### Show a tag

```http
  GET /api/tag/{id}
```

### Create a new tag

```http
  POST /api/tag/

  Content-Type: appliation/json
```

| Parameter | Type     | Description                                                                       |
|:----------|:---------|:----------------------------------------------------------------------------------|
| `name`    | `string` | **Required**.  The title of the question **Length** min:2 max:30 **Unique**. name |

### Delete a tag

```http
  DELETE /api/tag/{id}
```