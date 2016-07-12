<?php

namespace ActuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Actu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="RN\ActuBundle\Entity\ActuRepository")
 * 
 * @ORM\HasLifecycleCallbacks
 */
class Actu {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="css", type="string", length=255, nullable=true)
     */
    private $css;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255)
     */
    private $auteur;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publier", type="boolean")
     */
    private $publier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateedition", type="datetime")
     */
    private $dateedition;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datemodif", type="datetime")
     */
    private $datemodif;

    /**
     * @var string
     *
     * @ORM\Column(name="actuXsTa", type="text", nullable=true)
     */
    private $actuXsTa;

    /**
     * @var string
     *
     * @ORM\Column(name="actuSmTa", type="text", nullable=true)
     */
    private $actuSmTa;

    /**
     * @var string
     *
     * @ORM\Column(name="actuMdTa", type="text", nullable=false)
     */
    private $actuMdTa;

    /**
     * @var string
     *
     * @ORM\Column(name="actuLgTa", type="text", nullable=false)
     */
    private $actuLgTa;

    /**
     * @var string
     *
     * @ORM\Column(name="actuMini", type="text", nullable=true)
     */
    private $actuMini;

    /**
     * @var string
     *
     * @ORM\Column(name="actuMiniPhoto", type="string", length=255, nullable=true)
     */
    private $actuMiniPhoto;

    /**
     * @ORM\ManyToMany(targetEntity="RN\PhototekBundle\Entity\Photo", inversedBy="actu")
     * @ORM\JoinColumn(nullable=true)
     */
    private $photo;

    /**
     * @var array
     *
     * @ORM\Column(name="listeidLG", type="array", nullable=true)
     */
    private $listeidLG = [];

    /**
     * @var array
     *
     * @ORM\Column(name="listeidMD", type="array", nullable=true)
     */
    private $listeidMD = [];

    /**
     * @var array
     *
     * @ORM\Column(name="listeidSM", type="array", nullable=true)
     */
    private $listeidSM = [];

    /**
     * @var array
     *
     * @ORM\Column(name="listeidXS", type="array", nullable=true)
     */
    private $listeidXS = [];

    private $listeimg;
    private $listeidimg;

    
    public function getlisteidimg() {
        return $this->listeidimg;
    }

    public function setlisteidimg($a) {
        $this->listeidimg = $a;
    }
 
    public function getlisteimg() {
        return $this->listeimg;
    }

    public function setlisteimg($img) {
        $this->listeimg = $img;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Actu
     */
    public function setTitre($titre) {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre() {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Actu
     */
    public function setContenu($contenu) {
        // supprime _mini sur les photos
        $contenusansmini = preg_replace('#(_mini\.)#', '.', $contenu);

        //extractalt: recuperation id sur img
        //$idetalt[id]=contenu img <img .../>
        //stocker dans listeid
        $idetalt = $this->extractalt($contenusansmini);
        if (!empty($idetalt))
        //extractslugimg capture balise alt et stock dans listeimg
            $this->extractslugimg($idetalt);
        //extractlistesrcimg capture chemin relative et stock dans Listeidimg
        $this->extractlistesrcimg($idetalt);
        $contenusansmini = $this->clearbalise($contenusansmini);
        $arrayidstyle = [];
        //extraction des balise css
        // $arrayidstyle[id]=array[balise=>valeur]

        foreach ($idetalt as $key => $value) {
            $arrayidstyle[$key] = $this->extractcss($value);
        }
        $this->SetListeidLG($arrayidstyle);
        $this->contenu = $contenusansmini;
        return $this;
    }

    public function extractslugimg($listephoto) {
        $listeslugimg = [];
        foreach ($listephoto as $key => $value) {
            preg_match('/alt="(.+?)"/i', $value, $matchesstyle);
            $listeslugimg[] = $matchesstyle[1];
        }
        $this->setlisteimg($listeslugimg);
    }

    public function extractlistesrcimg($idetalt) {
        $listeslugimg = [];
        foreach ($idetalt as $key => $value) {
            preg_match('/uploads\/img\/([a-z0-9]+)./i', $value, $matchesstyle);
            $listeslugimg[] = $matchesstyle[1];
        }
        $this->setListeidimg($listeslugimg);
    }

    public function extractalt($contenusansmini) {
        $listeid = [];
        $i = 0;
        preg_match_all('/<img(.+?)id="([0-9i]*)?"(.+?)>/i', $contenusansmini, $matches);
        foreach ($matches[2] as $key1 => $value1) {
            $listeid[$value1] = $matches[0][$i];

            $i++;
        }
        return $listeid;
    }

    public function clearbalise($imgavecbalise) {
        $arrayregex = [];
        $arrayregex[] = '/(<img.+?)( height=".+?")(.+?>)/i';
        $arrayregex[] = '/(<img.+?)( width=".+?")(.+?>)/i';
        $arrayregex[] = '/(<img.+?)( style=".+?")(.+?>)/i';
        $arrayregex[] = '/(<img.+?)( class=".+?")(.+?>)/i';

        $imgavecbalise = preg_replace_callback($arrayregex, function($matches) {
            return $matches[1] . "" . $matches[3];
        }, $imgavecbalise);
        return $imgavecbalise;
    }

    public function clearbalisefull($imgavecbalise) {
        $arrayregex = [];
        $arrayregex[] = '/(<img.+?)( height=".+?")(.+?>)/i';
        $arrayregex[] = '/(<img.+?)( width=".+?")(.+?>)/i';
        $arrayregex[] = '/(<img.+?)( style=".+?")(.+?>)/i';
        $arrayregex[] = '/(<img.+?)( id=".+?")(.+?>)/i';

        $imgavecbalise = preg_replace_callback($arrayregex, function($matches) {
            return $matches[1] . " class='image_article_mini' " . $matches[3];
        }, $imgavecbalise);
        return $imgavecbalise;
    }

    public function extractcss($alt) {
        $style = [];
        if (preg_match('/class="marginauto"/i', $alt, $matches)) {
            $style["margin-right"] = "auto";
            $style["margin-left"] = "auto";
            $style["display"] = "block";
        }
        if (preg_match('/style="([^"].+?)"/i', $alt, $matchesstyle)) {

            if (preg_match('/( width):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
            if (preg_match('/(height):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
            if (preg_match('/(border-width):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
            if (preg_match('/(border-style):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
            if (preg_match('/(margin):(.+?);/i', $matchesstyle[1], $matches)) {

                if (empty($style["margin-left"])) {
                    $style[$matches[1]] = $matches[2];
                } else {
                    if (preg_match('/([0-9]*)(px|em|%) ([0-9]*)(px|em|%)/', $matches[2], $matches2)) {
                        $style["margin-top"] = $matches2[1] . $matches2[2];
                        $style["margin-buttom"] = $matches2[1] . $matches2[2];
                    } else {
                        $style["margin-top"] = $matches[2];
                        $style["margin-buttom"] = $matches[2];
                    }
                }
            }

            if (preg_match('/(margin-left):(.+?);/i', $matchesstyle[1], $matches)) {
                if (empty($style["margin-left"])) {
                    $style[$matches[1]] = $matches[2];
                }
            }
            if (preg_match('/(margin-right):(.+?);/i', $matchesstyle[1], $matches)) {
                if (empty($style["margin-right"])) {
                    $style[$matches[1]] = $matches[2];
                }
            }

            if (preg_match('/(margin-top):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
            if (preg_match('/(margin-bottom):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
            if (preg_match('/(float):(.+?);/i', $matchesstyle[1], $matches)) {
                $style[$matches[1]] = $matches[2];
            }
        }
        if (preg_match('/style="(width)?:(.+?);/i', $alt, $matches)) {
            $style[$matches[1]] = $matches[2];
        }
        if (preg_match('/height="([^"].+?)"/i', $alt, $matches)) {
            $style["height"] = $matches[1] . "px";
        }
        if (preg_match('/width="([^"].+?)"/i', $alt, $matches)) {
            $style["width"] = $matches[1] . "px";
        }
        return $style;
    }

    /**
     * 
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function makecss() {

        $csslarge = $this->getListeidLG();
        $cssl = "";
        $cssmedium = $this->getListeidMD();
        $cssm = "";
        $csssmall = $this->getListeidSM();
        $csss = "";
        $cssextra = $this->getListeidXS();
        $cssxs = "";

//construit le css

        if (!empty($csslarge)) {

            foreach ($csslarge as $key => $value) {
                $cssl = $cssl . "#" . $key . "{\r\n ";
                foreach ($value as $keys => $values) {
                    $cssl = $cssl . $keys . ":" . $values . "; \r\n ";
                }
                $cssl = $cssl . "}\r\n ";
            }
        }
        if (!empty($cssmedium)) {

            foreach ($cssmedium as $key => $value) {
                $cssm = $cssm . "#" . $key . "{\r\n ";
                foreach ($value as $keys => $values) {
                    $cssm = $cssm . $keys . ":" . $values . "; \r\n ";
                }
                $cssm = $cssm . "}\r\n ";
            }
        }
        if (!empty($csssmall)) {
            foreach ($csssmall as $key => $value) {
                $csss = $csss . "#" . $key . "{\r\n ";
                foreach ($value as $keys => $values) {
                    $csss = $csss . $keys . ":" . $values . "; \r\n ";
                }
                $csss = $csss . "}\r\n ";
            }
        }
        if (!empty($cssextra)) {
            foreach ($cssextra as $key => $value) {
                $cssxs = $cssxs . "#" . $key . "{\r\n ";
                foreach ($value as $keys => $values) {
                    $cssxs = $cssxs . $keys . ":" . $values . "; \r\n ";
                }
                $cssxs = $cssxs . "}\r\n ";
            }
        }
        $contenufichiercss = "@media screen and (min-width: 1200px) {\r\n" . $cssl . "\r\n  }
@media screen and (min-width: 992px) and (max-width: 1199px) {\r\n" . $cssm . "\r\n  }
@media screen and (min-width: 768px) and (max-width: 991px){\r\n " . $csss . "\r\n }
@media screen and (max-width: 767px){\r\n " . $cssxs . "\r\n }";

        $nomfichiercss = $this->getCss();
        file_put_contents(__DIR__ . '/../../../../web/' . $nomfichiercss, $contenufichiercss);
    }

    /**
     * @ORM\PostRemove()
     * @ORM\PreUpdate()
     */
    public function removedfile() {
        if (!empty($this->getCss())) {
            if (file_exists(__DIR__ . '/../../../../web/css/cssarticles/' . $this->getCss())) {


                unlink(__DIR__ . '/../../../../web/css/cssarticles/' . $this->getCss());
            }
        }
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu() {
        return $this->contenu;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     * @return Actu
     */
    public function setAuteur($auteur) {
        $this->auteur = $auteur;
        return $this;
    }

    /**
     * Get auteur
     *
     * @return string 
     */
    public function getAuteur() {
        return $this->auteur;
    }

    /**
     * Set publier
     *
     * @param boolean $publier
     * @return Actu
     */
    public function setPublier($publier) {
        $this->publier = $publier;
        return $this;
    }

    /**
     * Get publier
     *
     * @return boolean 
     */
    public function getPublier() {
        return $this->publier;
    }

    /**
     * Set dateedition
     *
     * @param \DateTime $dateedition
     * @return Actu
     */
    public function setDateedition($dateedition) {
        $this->dateedition = $dateedition;
        return $this;
    }

    /**
     * Get dateedition
     *
     * @return \DateTime 
     */
    public function getDateedition() {
        return $this->dateedition;
    }

    /**
     * Set datemodif
     *
     * @param \DateTime $datemodif
     * @return Actu
     */
    public function setDatemodif($datemodif) {
        $this->datemodif = $datemodif;
        return $this;
    }

    /**
     * Get datemodif
     *
     * @return \DateTime 
     */
    public function getDatemodif() {
        return $this->datemodif;
    }

    public function __construct() {
        $this->illustrations = new ArrayCollection();
        $this->photo = new ArrayCollection();
        $this->setDatemodif(new \DateTime());
        $this->setDateedition(new \DateTime());
    }

    /**
     * Set css
     *
     * @param string $css
     * @return Actu
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setCss() {
        $nomfichiercss = "css/cssarticles/" . time() . mt_rand(0, 100) . ".css";
        $this->css = ($nomfichiercss);
        return $this;
    }

    /**
     * Get css
     *
     * @return string 
     */
    public function getCss() {
        return $this->css;
    }

    /**
     * Set actuSmTa
     *
     * @param string $actuSmTa
     * @return Actu
     */
    public function setActuSmTa($actuSmTa) {
        $actusm = html_entity_decode($actuSmTa);
        // supprime _mini sur les photos
        $actusm = preg_replace('#(_mini\.)#', '.', $actusm);
        // recuperation id sur img
        //$idetalt[id]=alt
        $idetalt = $this->extractalt($actusm);
        $actusm = $this->clearbalise($actusm);
        $arrayidstyle = [];
        //extraction des balise css
        // $arrayidstyle[id]=array[balise=>valeur]
        foreach ($idetalt as $key => $value) {
            $arrayidstyle[$key] = $this->extractcss($value);
        }
        $this->setListeidSM($arrayidstyle);
        $this->actuSmTa = $actuSmTa;
        return $this;
    }

    /**
     * Get actuSmTa
     *
     * @return string 
     */
    public function getActuSmTa() {
        return $this->actuSmTa;
    }

    /**
     * Set actuXsTa
     *
     * @param string $actuXsTa
     * @return Actu
     */
    public function setActuXsTa($actuXsTa) {
        // supprime _mini sur les photos
        $contenusansmini = preg_replace('#(_mini\.)#', '.', $actuXsTa);
        // recuperation id sur img
        //$idetalt[id]=alt
        $idetalt = $this->extractalt($contenusansmini);
        $contenusansmini = $this->clearbalise($contenusansmini);
        $arrayidstyle = [];
        //extraction des balise css
        // $arrayidstyle[id]=array[balise=>valeur]
        foreach ($idetalt as $key => $value) {
            $arrayidstyle[$key] = $this->extractcss($value);
        }
        $this->setListeidXS($arrayidstyle);
        $this->actuXsTa = $actuXsTa;
        return $this;
    }

    /**
     * Get actuXsTa
     *
     * @return string 
     */
    public function getActuXsTa() {
        return $this->actuXsTa;
    }

    /**
     * Set actuMdTa
     *
     * @param string $actuMdTa
     * @return Actu
     */
    public function setActuMdTa($actuMdTa) {

        // supprime _mini sur les photos
        $actumd = html_entity_decode($actuMdTa);
        $contenusansmini = preg_replace('#(_mini\.)#', '.', $actumd);
        // recuperation id sur img
        $idetalt = $this->extractalt($contenusansmini);
        $contenusansmini = $this->clearbalise($contenusansmini);
        $arrayidstyle = [];
        //extraction des balise css
        foreach ($idetalt as $key => $value) {
            $arrayidstyle[$key] = $this->extractcss($value);
        }
        $this->setListeidMD($arrayidstyle);
        $this->actuMdTa = $actuMdTa;
        return $this;
    }

    /**
     * Get actuMdTa
     *
     * @return string 
     */
    public function getActuMdTa() {
        return $this->actuMdTa;
    }

    /**
     * Set actuLgTa
     *
     * @param string $actuLgTa
     * @return Actu
     */
    public function setActuLgTa($actuLgTa) {
        $this->setContenu(html_entity_decode($actuLgTa));
        $this->actuLgTa = $actuLgTa;
        return $this;
    }

    /**
     * Get actuLgTa
     *
     * @return string 
     */
    public function getActuLgTa() {
        return $this->actuLgTa;
    }

    /**
     * Set actuMini
     *
     * @param string $actuMini
     * @return Actu
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setActuMini($actuMini) {
        $contenu_brut = $this->getActuLgTa();
        //capture image
        preg_match('/(<img.+?>)/i', $contenu_brut, $img);
        if (!empty($img)) {
            $capt_img = $this->clearbalisefull($img[1]);

            $this->setActuMiniPhoto($capt_img);
        } else {
            $this->setActuMiniPhoto(Null);
        }
        //capture texte
        //supp image des balises P
        $text_No_Img = preg_replace('/(<im.+?>)/i', '', $contenu_brut);
        //supprimer liens
        $text_No_link = preg_replace('/(<a(?:.)*?<\/a>)/i', ' ', $text_No_Img);
        //supprimer retours chariots
        $text_No_Chariot = preg_replace('/(<br \/>)/i', ' ', $text_No_link);
        $capt_text = [];
        //supp les balises P vide     
        preg_match_all('/<p.*?>(.*?)<\/p>/is', $text_No_Chariot, $balise);
        $textNonVide = [];
        foreach ($balise[1] as $key => $value) {
            if ($value !== "&nbsp;") {
                array_push($textNonVide, $value);
            }
        }
        //capture texte 
        $capt_text = implode(" ", $textNonVide);
        //400 longeur text pour article mini
        if (strlen($capt_text) >= 451) {
            $capt_text = substr($capt_text, 0, 450);
        } else {
            $capt_text = substr($capt_text, 0, strlen($capt_text));
        }
        $capt_text = $capt_text . "...";
        $this->actuMini = $capt_text;
        return $this;
    }

    /**
     * Get actuMini
     *
     * @return string 
     */
    public function getActuMini() {
        return $this->actuMini;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Actu
     */
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Set actuMiniPhoto
     *
     * @param string $actuMiniPhoto
     * @return Actu
     */
    public function setActuMiniPhoto($actuMiniPhoto) {
        $this->actuMiniPhoto = $actuMiniPhoto;
        return $this;
    }

    /**
     * Get actuMiniPhoto
     *
     * @return string 
     */
    public function getActuMiniPhoto() {
        return $this->actuMiniPhoto;
    }

    /**
     * Add photo
     *
     * @param \RN\PhototekBundle\Entity\Photo $photo
     * @return Actu
     */
    public function addPhoto(\RN\PhototekBundle\Entity\Photo $photo) {
        $this->photo[] = $photo;
        $photo->addActu($this);
        return $this;
    }

    /**
     * Remove photo
     *
     * @param \RN\PhototekBundle\Entity\Photo $photo
     */
    public function removePhoto(\RN\PhototekBundle\Entity\Photo $photo) {
        $this->photo->removeElement($photo);
    }

    /**
     * Get photo
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhoto() {
        return $this->photo;
    }

    public function clearPhoto() {
        $this->getPhoto()->clear();
    }

    /**
     * Set listeidLG
     *
     * @param array $listeidLG
     * @return Actu
     */
    public function setListeidLG($listeidLG) {
        $this->listeidLG = $listeidLG;
        return $this;
    }

    /**
     * Get listeidLG
     *
     * @return array 
     */
    public function getListeidLG() {
        return $this->listeidLG;
    }

    /**
     * Set listeidMD
     *
     * @param array $listeidMD
     * @return Actu
     */
    public function setListeidMD($listeidMD) {
        $this->listeidMD = $listeidMD;
        return $this;
    }

    /**
     * Get listeidMD
     *
     * @return array 
     */
    public function getListeidMD() {
        return $this->listeidMD;
    }

    /**
     * Set listeidSM
     *
     * @param array $listeidSM
     * @return Actu
     */
    public function setListeidSM($listeidSM) {
        $this->listeidSM = $listeidSM;
        return $this;
    }

    /**
     * Get listeidSM
     *
     * @return array 
     */
    public function getListeidSM() {
        return $this->listeidSM;
    }

    /**
     * Set listeidXS
     *
     * @param array $listeidXS
     * @return Actu
     */
    public function setListeidXS($listeidXS) {
        $this->listeidXS = $listeidXS;
        return $this;
    }

    /**
     * Get listeidXS
     *
     * @return array 
     */
    public function getListeidXS() {
        return $this->listeidXS;
    }

}
