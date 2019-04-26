<?php

namespace FormArmorBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="FormArmorBundle\Repository\ClientRepository")
 */
class Client extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

	/**
	 * @ORM\ManyToOne (targetEntity="FormArmorBundle\Entity\Statut")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $statut;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=60)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=6)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=50)
     */
    private $ville;

    /**
     * @var int
     *
     * @ORM\Column(name="nbhcpta", type="smallint")
     */
    private $nbhcpta;

    /**
     * @var int
     *
     * @ORM\Column(name="nbhbur", type="smallint")
     */
    private $nbhbur;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Client
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Client
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Client
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set nbhcpta
     *
     * @param integer $nbhcpta
     *
     * @return Client
     */
    public function setNbhcpta($nbhcpta)
    {
        $this->nbhcpta = $nbhcpta;

        return $this;
    }

    /**
     * Get nbhcpta
     *
     * @return int
     */
    public function getNbhcpta()
    {
        return $this->nbhcpta;
    }

    /**
     * Set nbhbur
     *
     * @param integer $nbhbur
     *
     * @return Client
     */
    public function setNbhbur($nbhbur)
    {
        $this->nbhbur = $nbhbur;

        return $this;
    }

    /**
     * Get nbhbur
     *
     * @return int
     */
    public function getNbhbur()
    {
        return $this->nbhbur;
    }

    /**
     * Set statut
     *
     * @param \FormArmorBundle\Entity\Statut $statut
     *
     * @return Client
     */
    public function setStatut(\FormArmorBundle\Entity\Statut $statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return \FormArmorBundle\Entity\Statut
     */
    public function getStatut()
    {
        return $this->statut;
    }
}
