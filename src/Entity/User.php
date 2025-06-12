<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Task;
use App\Entity\Education;
use App\Entity\WorkExperience;
use App\Entity\Skill;
use App\Entity\CvUpload;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50, minMessage: 'Your username must be at least {{ limit }} characters long', maxMessage: 'Your username cannot be longer than {{ limit }} characters')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9_-]+$/', message: 'Your username can only contain letters, numbers, underscores and dashes')]
    private ?string $username = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: 'First name cannot be longer than {{ limit }} characters')]
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: 'Last name cannot be longer than {{ limit }} characters')]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: 'Job title cannot be longer than {{ limit }} characters')]
    private ?string $jobTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'Please enter a valid LinkedIn URL')]
    #[Assert\Length(max: 255, maxMessage: 'LinkedIn URL cannot be longer than {{ limit }} characters')]
    private ?string $linkedinUrl = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'assignedTo', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Education::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $educations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WorkExperience::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $workExperiences;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Skill::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $skills;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: CvUpload::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private ?CvUpload $cvUpload = null;

    // Cache for profile completion percentage to avoid recalculation
    private ?int $cachedProfileCompletion = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->educations = new ArrayCollection();
        $this->workExperiences = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        // Use email as the identifier for authentication
        return (string) $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        $this->clearProfileCompletionCache();

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;
        $this->clearProfileCompletionCache();

        return $this;
    }

    public function getFullName(): ?string
    {
        $parts = array_filter([$this->firstName, $this->lastName]);
        return empty($parts) ? null : implode(' ', $parts);
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;
        $this->clearProfileCompletionCache();

        return $this;
    }

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function setLinkedinUrl(?string $linkedinUrl): static
    {
        $this->linkedinUrl = $linkedinUrl;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles()) || $this->email === 'admin@jems.com';
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear any sensitive data here
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setAssignedTo($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAssignedTo() === $this) {
                $task->setAssignedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Education>
     */
    public function getEducations(): Collection
    {
        return $this->educations;
    }

    public function addEducation(Education $education): static
    {
        if (!$this->educations->contains($education)) {
            $this->educations->add($education);
            $education->setUser($this);
            $this->clearProfileCompletionCache();
        }

        return $this;
    }

    public function removeEducation(Education $education): static
    {
        if ($this->educations->removeElement($education)) {
            if ($education->getUser() === $this) {
                $education->setUser(null);
            }
            $this->clearProfileCompletionCache();
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkExperience>
     */
    public function getWorkExperiences(): Collection
    {
        return $this->workExperiences;
    }

    public function addWorkExperience(WorkExperience $workExperience): static
    {
        if (!$this->workExperiences->contains($workExperience)) {
            $this->workExperiences->add($workExperience);
            $workExperience->setUser($this);
            $this->clearProfileCompletionCache();
        }

        return $this;
    }

    public function removeWorkExperience(WorkExperience $workExperience): static
    {
        if ($this->workExperiences->removeElement($workExperience)) {
            if ($workExperience->getUser() === $this) {
                $workExperience->setUser(null);
            }
            $this->clearProfileCompletionCache();
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->setUser($this);
            $this->clearProfileCompletionCache();
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        if ($this->skills->removeElement($skill)) {
            if ($skill->getUser() === $this) {
                $skill->setUser(null);
            }
            $this->clearProfileCompletionCache();
        }

        return $this;
    }

    public function getCvUpload(): ?CvUpload
    {
        return $this->cvUpload;
    }

    public function setCvUpload(?CvUpload $cvUpload): static
    {
        if ($cvUpload === null && $this->cvUpload !== null) {
            $this->cvUpload->setUser(null);
        }

        if ($cvUpload !== null && $cvUpload->getUser() !== $this) {
            $cvUpload->setUser($this);
        }

        $this->cvUpload = $cvUpload;
        $this->clearProfileCompletionCache();

        return $this;
    }

    public function getProfileCompletionPercentage(): int
    {
        // Return cached value if available
        if ($this->cachedProfileCompletion !== null) {
            return $this->cachedProfileCompletion;
        }

        $score = 0;
        $maxScore = 8;

        // Basic info (4 points)
        if ($this->firstName) $score++;
        if ($this->lastName) $score++;
        if ($this->jobTitle) $score++;
        if ($this->email) $score++; // Always present

        // Profile sections (4 points) - use isEmpty() to avoid triggering count() queries
        if (!$this->educations->isEmpty()) $score++;
        if (!$this->workExperiences->isEmpty()) $score++;
        if (!$this->skills->isEmpty()) $score++;
        if ($this->cvUpload) $score++;

        // Cache the result
        $this->cachedProfileCompletion = (int) round(($score / $maxScore) * 100);

        return $this->cachedProfileCompletion;
    }

    /**
     * Clear the cached profile completion percentage when profile data changes
     */
    public function clearProfileCompletionCache(): void
    {
        $this->cachedProfileCompletion = null;
    }
}
