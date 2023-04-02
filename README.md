# Getting started

## Installation

Clone the repository

    git clone git@github.com:MilSimonyan/UTeamForum.git forum

Switch to the repo folder

    cd forum

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**) [Environment variables](#environment-variables)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone git@github.com:MilSimonyan/UTeamForum.git forum
    cd forum
    composer install
    cp .env.example .env
    php artisan key:generate

**Make sure you set the correct database connection information before running the migrations**

    php artisan migrate
    php artisan serve

## Dependencies

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application
fully working.

----------

# Authentication

***SSO*** : Single Sign-On (SSO) is a method of authentication that allows a user to access multiple applications or
systems with a single set of login credentials, such as a username and password. This eliminates the need for the user
to remember multiple sets of login information and can improve security by reducing the number of times a user needs to
enter sensitive information, such as a password.

- The SSO system will then authenticate the user and create a session, which is used to track the user's activity across
  multiple applications or systems. When the user attempts to access another application or system that is protected by
  SSO, the system will check the session to see if the user has already been authenticated and allow the user to access
  the application or system without requiring the user to enter login credentials again.

----------

# API Reference

## POST

### Get all(0-10) posts. To sort the records in descending order of created_at

```http
  GET /api/post?courseId={id}
```

### Paginate posts. To sort the records in descending order of created_at

```http
  GET /api/post?courseId={id}from=0&offset=5
```

### Create a new post

```http
  POST /api/post/

  Content-Type: multipart/form-data
```

| Parameter  | Type            | Description                                                      |
|:-----------|:----------------|:-----------------------------------------------------------------|
| `title`    | `string`        | **Required**.  The title of the post **Length** min:3 max:100    |
| `content`  | `string`        | **Required**.  The content of the post **Length** min:3 max:3000 |
| `media`    | `mimes`         | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                       |
| `tags`     | `array:strings` | **Optional**.  The tag of the post                               |
| `courseId` | `int`           | **Required**. Must be exists                                     |

### Update a post

```http
  POST /api/post/{id}

  Content-Type: multipart/form-data
```

| Parameter | Type            | Description                                                      |
|:----------|:----------------|:-----------------------------------------------------------------|
| `title`   | `string`        | **Optional**.  The title of the post **Length** min:3 max:100    |
| `content` | `string`        | **Optional**.  The content of the post **Length** min:3 max:3000 |
| `media`   | `mimes`         | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                       |
| `tags`    | `array:strings` | **Optional**.  The tag of the post                               |

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

### Get all(0-10) questions. To sort the records in descending order of created_at

```http
  GET /api/question?courseId={id}
```

### Paginate questions. To sort the records in descending order of created_at

```http
  GET /api/question?courseId={id}&from=0&offset=10
```

### Create a new question

```http
  POST /api/question/

  Content-Type: multipart/form-data
```

| Parameter  | Type            | Description                                                          |
|:-----------|:----------------|:---------------------------------------------------------------------|
| `title`    | `string`        | **Required**.  The title of the question **Length** min:3 max:100    |
| `content`  | `string`        | **Required**.  The content of the question **Length** min:3 max:3000 |
| `media`    | `mimes`         | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                           |
| `tags`     | `array:strings` | **Optional**.  The tags of the question                              |
| `courseId` | `int`           | **Required**. Must be exists                                         |

### Update a question you can update question 5 minutes after adding

```http
  POST /api/question/{id}

  Content-Type: multipart/form-data
```

| Parameter | Type            | Description                                                          |
|:----------|:----------------|:---------------------------------------------------------------------|
| `title`   | `string`        | **Optional**.  The title of the question **Length** min:3 max:100    |
| `content` | `string`        | **Optional**.  The content of the question **Length** min:3 max:3000 |
| `media`   | `mimes`         | **Optional**. jpg,jpeg,png,gif,mp4,mov,ogg                           |
| `tags`    | `array:strings` | **Optional**.  The tags of the question                              |

### Get question comments(0-5). To sort the records in descending order of created_at

```http
  GET /api/question/{id}/comments
```

### Paginate question comments. To sort the records in descending order of created_at

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

## TAGS

### Get all(0-10) tags. To sort the records in descending order of created_at

```http
  GET /api/tag?courseId={id}
```

### Paginate tags. To sort the records in descending order of created_at

```http
  GET /api/tag?courseId={id}&from=0&offset=10
```

### Show a tag

```http
  GET /api/tag/{id}
```

### Create a new tag (Tag is created with Question and Post) 

```http
  POST /api/tag/

  Content-Type: appliation/json
```

| Parameter  | Type     | Description                                                                 |
|:-----------|:---------|:----------------------------------------------------------------------------|
| `name`     | `string` | **Required**.  The name of the tag **Length** min:2 max:30 **Unique**. name |
| `courseId` | `int`    | **Required**                                                                |


### Paginate a forum items where have selected tag. To sort the records in descending order of created_at

```http
  GET /api/tag/{tag_name}/forum-items?courseId={id}&from=0&offset=10
```

## FORUM

### Get all(0-10) forum items(questions & posts) To sort the records in descending order of created_at

```http
  GET /api/forum?courseId=?
```

### Get a forum items where have selected tag(tagName). To sort the records in descending order of created_at

```http
  GET /api/forum?courseId=?&filter={tagName}
```

### Paginate forum items(questions & posts) To sort the records in descending order of created_at

```http
  GET /api/forum?courseId=?&from=0&offset=10
```