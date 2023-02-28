<?php

namespace App\Model;

class Filtre
{
    private $campus;

    private $search;
    private $dateDebut;
    private $dateLimite;

    private $estOrganisateur;

    private $estInscrit;

    private $pasInscrit;

    private $estPassees;

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search): void
    {
        $this->search = $search;
    }

    /**
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param mixed $dateDebut
     */
    public function setDateDebut($dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return mixed
     */
    public function getDateLimite()
    {
        return $this->dateLimite;
    }

    /**
     * @param mixed $dateLimite
     */
    public function setDateLimite($dateLimite): void
    {
        $this->dateLimite = $dateLimite;
    }

    /**
     * @return mixed
     */
    public function getEstOrganisateur()
    {
        return $this->estOrganisateur;
    }

    /**
     * @param mixed $estOrganisateur
     */
    public function setEstOrganisateur($estOrganisateur): void
    {
        $this->estOrganisateur = $estOrganisateur;
    }

    /**
     * @return mixed
     */
    public function getEstInscrit()
    {
        return $this->estInscrit;
    }

    /**
     * @param mixed $estInscrit
     */
    public function setEstInscrit($estInscrit): void
    {
        $this->estInscrit = $estInscrit;
    }

    /**
     * @return mixed
     */
    public function getPasInscrit()
    {
        return $this->pasInscrit;
    }

    /**
     * @param mixed $pasInscrit
     */
    public function setPasInscrit($pasInscrit): void
    {
        $this->pasInscrit = $pasInscrit;
    }

    /**
     * @return mixed
     */
    public function getEstPassees()
    {
        return $this->estPassees;
    }

    /**
     * @param mixed $estPassees
     */
    public function setEstPassees($estPassees): void
    {
        $this->estPassees = $estPassees;
    }


}