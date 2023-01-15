<?php

namespace App\Entities;

use App\Facades\HttpCaller;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;
use RuntimeException;
use stdClass;

class User implements Jsonable, JsonSerializable, Authenticatable
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $firstName;

    /**
     * @var string
     */
    private string $lastName;

    /**
     * @var string
     */
    private string $patronymic;

    /**
     * @var string|null
     */
    private ?string $birthDate;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $role;

    /**
     * @var string|null
     */
    private ?string $position;

    /**
     * @var \Illuminate\Support\Collection|null
     */
    private ?Collection $courses;

    /**
     * @var \Illuminate\Support\Collection|null
     */
    private ?Collection $departments;

    public function __construct()
    {
        $this->courses = new Collection();
        $this->departments = new Collection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     */
    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return string|null
     */
    public function getBirthDate(): string|null
    {
        return $this->birthDate;
    }

    /**
     * @param string $birthDate
     */
    public function setBirthDate(string $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string|null
     */
    public function getPosition(): string|null
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    /**
     * @param Collection|null $courses
     */
    public function setCourses(Collection $courses = null): void
    {
        try {
            $this->courses = $this->toCollection(
                HttpCaller::get(
                    sprintf(
                        'http://account.u-team.com/api/%s/%s',
                        $this->role,
                        $this->role == 'student' ? 'course' : 'courses'
                    )
                ) ?? $courses
            );
        } catch (\Exception $e) {
            $this->courses = null;
        }

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    /**
     * @param Collection|null $departments
     */
    public function setDepartments(Collection $departments = null): void
    {
        try {
            $this->departments = $this->toCollection(
                HttpCaller::get(
                    sprintf(
                        'http://account.u-team.com/api/%s/%s',
                        $this->role,
                        'department'
                    )
                ) ?? $departments
            );
        } catch (\Exception $e) {
            $this->departments = null;
        }
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return int
     */
    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        throw new RuntimeException('Method is not relevant for our authentication mechanism');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        throw new RuntimeException('Method is not relevant for our authentication mechanism');
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken($value): void
    {
        throw new RuntimeException('Method is not relevant for our authentication mechanism');
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        throw new RuntimeException('Method is not relevant for our authentication mechanism');
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'email' => $this->getEmail(),
            'patronymic' => $this->getPatronymic(),
            'birthDate' => $this->getBirthDate(),
            'role' => $this->getRole(),
            'position' => $this->getPosition(),
            'courses' => $this->getCourses(),
            'departments' => $this->getDepartments()
        ];
    }

    /**
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = 0): false|string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @param stdClass $stdClass
     * @return Authenticatable
     *
     * @throws \Exception
     */
    public function fromStdClass(stdClass $stdClass): Authenticatable
    {
        $this->id = $stdClass->id;
        $this->firstName = $stdClass->firstName;
        $this->lastName = $stdClass->lastName;
        $this->email = $stdClass->email;
        $this->patronymic = $stdClass->patronymic;
        $this->birthDate = $stdClass->birthDate ?? null;
        $this->role = $stdClass->role ?? null;
        $this->position = $stdClass->position ?? null;
        $this->setCourses();
        $this->setDepartments();

        return $this;
    }

    /**
     * @param $data
     *
     * @return Collection
     */
    private function toCollection($data): Collection
    {
        return collect(json_decode(json_encode($data), true));
    }
}
