<?php
/**
 * PHPDish comment component
 * @author Tao <taosikai@yeah.net>
 */
namespace PHPDish\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use PHPDish\Bundle\CoreBundle\Model\DateTimeTrait;
use PHPDish\Bundle\CoreBundle\Model\EnabledTrait;
use PHPDish\Bundle\CoreBundle\Model\IdentifiableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use PHPDish\Bundle\UserBundle\Model\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    use IdentifiableTrait, DateTimeTrait, EnabledTrait;

    /**
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="smallint", length=1);
     */
    protected $gender = 0;

    /**
     * @ORM\Column(type="string")
     */
    protected $description = '';

    /**
     * @ORM\Column(type="integer")
     */
    protected $followingCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $fanCount = 0;

    /**
     * 关注我的
     * @ORM\ManyToMany(targetEntity="PHPDish\Bundle\UserBundle\Entity\User", mappedBy="following")
     * @var ArrayCollection|UserInterface[]
     */
    protected $followers;

    /**
     * 我关注的
     * @ORM\ManyToMany(targetEntity="PHPDish\Bundle\UserBundle\Entity\User", inversedBy="followers")
     * @ORM\JoinTable(name="user_followers",
     *     joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="follower_id", referencedColumnName="id", unique=true)}
     * )
     * @var ArrayCollection|UserInterface[]
     */
    protected $following;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="PHPDish\Bundle\PostBundle\Entity\Category", inversedBy="subscribers")
     * @ORM\JoinTable(name="category_subscribers",
     *     joinColumns={@JoinColumn(name="category_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    protected $subscribedBlogs;

    /**
     * Constructor
     */
    public function __construct()
    {
        //关注我的
        $this->followers = new ArrayCollection();
        //我关注的
        $this->following =  new ArrayCollection();
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function avatar($width = 120, $height = 120)
    {
        return '/uploads/avatar/user1.jpg';
    }


    /**
     * Set email
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set password
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set gender
     * @param integer $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }


    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        if (is_null($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    /**
     * Set followingCount
     * @param integer $followingCount
     * @return User
     */
    public function setFollowingCount($followingCount)
    {
        $this->followingCount = $followingCount;

        return $this;
    }

    /**
     * Get followingCount
     * @return integer
     */
    public function getFollowingCount()
    {
        return $this->followingCount;
    }

    /**
     * Set fanCount
     * @param integer $fanCount
     * @return User
     */
    public function setFanCount($fanCount)
    {
        $this->fanCount = $fanCount;

        return $this;
    }

    /**
     * Get fanCount
     * @return integer
     */
    public function getFanCount()
    {
        return $this->fanCount;
    }

    /**
     * 检查用户是否被某个用户关注
     * @param UserInterface $user
     * @return boolean
     */
    public function isFollowedBy(UserInterface $user)
    {
        return $this->followers->contains($user);
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add follower
     *
     * @param User $follower
     *
     * @return User
     */
    public function addFollower(User $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower
     *
     * @param User $follower
     */
    public function removeFollower(User $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add following
     *
     * @param User $following
     *
     * @return User
     */
    public function addFollowing(User $following)
    {
        $this->following[] = $following;
        return $this;
    }

    /**
     * Remove following
     *
     * @param User $following
     */
    public function removeFollowing(User $following)
    {
        $this->following->removeElement($following);
    }

    /**
     * Get following
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * Add role
     *
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * Remove role
     * @param Role $role
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }
}
