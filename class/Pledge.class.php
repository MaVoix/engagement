<?php	/**
 * Class Pledge
 */
class Pledge	{

    private $aDataNonHydrate = array();
    private $aDataSet = array();
    private $callHydrateFromBDDOnGet = 0;

    private $_sDbInstance = null;

    private $nId;
    private $sDate_created;
    private $sDate_amended;
    private $sDate_deleted;
    private $sDate_completed;
    private $sCivility;
    private $sName;
    private $sFirstname;
    private $sAd1;
    private $sAd2;
    private $sAd3;
    private $sCity;
    private $sZipcode;
    private $sEmail;
    private $sTel;
    private $nAmount;
    private $nKey_edit;
    private $nGroup_id;
    private $sReference;


    /**
     * Constructeur
     * @param array $aParam tableau de parametres ( clé "id" pour instancier un pledge avec un id précis )
     * @param $sDbInstance (Opt) Nom de l'instance de la bdd à utiliser
     */
    public function __construct( $aParam=array(), $sDbInstance=null )
    {
        $this->hydrate($aParam);
        $this->nId = ( isset($aParam['id']) ) ? $aParam['id'] : 0;
        $this->_sDbInstance = $sDbInstance;
    }

    /**
     * Fonction permettant d'hydrater un objet
     * @param $aDonnees array tableau clé-valeur à hydrater ( par exemple "nom"=>"DUPONT" )
     */
    public function hydrate($aDonnees)
    {
        foreach ($aDonnees as $sKey => $sValue)
        {
            if(!is_int($sKey))
            {
                $sMethode = 'set'.ucfirst($sKey);
                if (method_exists($this, $sMethode))
                {
                    if( is_null($sValue) ) $sValue="";
                    $this->$sMethode($sValue);
                }
                else
                {
                    //echo "<br />Pledge->$sMethode() n'existe pas!";
                    $this->addDataNonHydrate($sKey,$sValue);
                }
            }
        }
    }

    /**
     * Fonction permettant d'hydrater un objet à partir d'une liste de champs (s'appuie sur l'id de l'objet)
     * @param $aFields array tableau contenant la liste des champs à hydrater ( '*' pour tous)
     */
    public function hydrateFromBDD($aFields=array())
    {
        if(count($aFields))
        {
            //hydrate uniquement les champs de base (pour le reste coder directement dans les acesseurs)
            $aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this->getId(),"pledge",$aFields);

            //hydrate l'objet
            $this->hydrate($aData);
        }
    }


    /**
     * Fonction permettant d'ajouter des données non-hydratées à l'objet
     * @param string $sKey champs
     * @param mixed $sValue valeur
     */
    public function addDataNonHydrate($sKey,$sValue)
    {
        $this->aDataNonHydrate[$sKey]=$sValue;
    }

    /**
     * Fonction permettant de récuperer des données non-hydratées à l'objet
     * @param string $sKey champs à récupérer
     * @return string valeur du champ
     */
    public function getDataNonHydrate($sKey)
    {
        if(isset($this->aDataNonHydrate[$sKey]))
        {
            return $this->aDataNonHydrate[$sKey];
        }
        else
        {
            return "";
        }
    }

    /**
     * Fonction permettant de supprimer fictivement un objet (en lui passant un date supprime)
     */
    public function supprime()
    {
        $this->setDate_deleted(date("Y-m-d H:i:s"));
        $this->save();
    }

    /**
     * Fonction permettant de supprimer réellement un objet (en faisant un DELETE )
     */
    public function delete()
    {
        $oReq=DbLink::getInstance($this->_sDbInstance)->prepare('DELETE FROM '."pledge".' WHERE  id=:id ');
        $oReq->execute(array("id"=>$this->getId()));
        $this->vide();
    }

    /**
     * Consulte la base de données pour savoir si l'objet existe, en le recherchant par son id
     * @static
     * @param int $nId Id de l'objet à chercher
     * @param $sDbInstance (Opt) Nom de l'instance de la bdd
     * @return bool Vrai si l'objet existe, Faux sinon
     */
    public static function exists($nId=0, $sDbInstance=null)
    {
        $oReq=DbLink::getInstance($sDbInstance)->prepare('SELECT id FROM '."pledge".' WHERE  id=:id ');
        $oReq->execute(array("id"=>$nId));
        $aRes=$oReq->getRow(0);
        return (count($aRes)!=0);
    }

    /**
     * Sauvegarde l'objet en base
     */
    public function save()
    {
        $aData=array();
        if(isset($this->aDataSet["date_created"]))
        {
            $aData["date_created"]=$this->getDate_created();
        }

        if(isset($this->aDataSet["date_amended"]))
        {
            $aData["date_amended"]=$this->getDate_amended();
        }

        if(isset($this->aDataSet["date_deleted"]))
        {
            $aData["date_deleted"]=$this->getDate_deleted();
        }

        if(isset($this->aDataSet["date_completed"]))
        {
            $aData["date_completed"]=$this->getDate_completed();
        }

        if(isset($this->aDataSet["civility"]))
        {
            $aData["civility"]=$this->getCivility();
        }

        if(isset($this->aDataSet["name"]))
        {
            $aData["name"]=$this->getName();
        }

        if(isset($this->aDataSet["firstname"]))
        {
            $aData["firstname"]=$this->getFirstname();
        }

        if(isset($this->aDataSet["ad1"]))
        {
            $aData["ad1"]=$this->getAd1();
        }

        if(isset($this->aDataSet["ad2"]))
        {
            $aData["ad2"]=$this->getAd2();
        }

        if(isset($this->aDataSet["ad3"]))
        {
            $aData["ad3"]=$this->getAd3();
        }

        if(isset($this->aDataSet["city"]))
        {
            $aData["city"]=$this->getCity();
        }

        if(isset($this->aDataSet["zipcode"]))
        {
            $aData["zipcode"]=$this->getZipcode();
        }

        if(isset($this->aDataSet["email"]))
        {
            $aData["email"]=$this->getEmail();
        }

        if(isset($this->aDataSet["tel"]))
        {
            $aData["tel"]=$this->getTel();
        }

        if(isset($this->aDataSet["amount"]))
        {
            $aData["amount"]=$this->getAmount();
        }

        if(isset($this->aDataSet["key_edit"]))
        {
            $aData["key_edit"]=$this->getKey_edit();
        }

        if(isset($this->aDataSet["group_id"]))
        {
            $aData["group_id"]=$this->getGroup_id();
        }

        if(isset($this->aDataSet["reference"]))
        {
            $aData["reference"]=$this->getReference();
        }

        if($this->getId()>0)
        {
            DbLink::getInstance($this->_sDbInstance)->update("pledge",$aData,' id="'.$this->getId().'" ');
        }
        else
        {
            $this->setId(DbLink::getInstance($this->_sDbInstance)->insert("pledge",$aData));
        }
        $this->aDataSet=array();
    }

    /**
     * Deshydrate complement l'objet, et vide la liste des champs à sauvegarder
     */
    private function vide()
    {
        $this->callHydrateFromBDDOnGet=0;
        $this->aDataSet=array();
        $this->setDate_created(NULL);
        $this->setDate_amended(NULL);
        $this->setDate_deleted(NULL);
        $this->setDate_completed(NULL);
        $this->setCivility(NULL);
        $this->setName(NULL);
        $this->setFirstname(NULL);
        $this->setAd1(NULL);
        $this->setAd2(NULL);
        $this->setAd3(NULL);
        $this->setCity(NULL);
        $this->setZipcode(NULL);
        $this->setEmail(NULL);
        $this->setTel(NULL);
        $this->setAmount(0);
        $this->setKey_edit(NULL);
        $this->setGroup_id(NULL);
        $this->setReference(NULL);
    }

    /**
     * Renvoie l'objet sous forme de chaine de caractère
     */
    public function __toString()
    {
        $aObjet = [
            "id" => $this->getId(),
            "date_created" => $this->getDate_created(),
            "date_amended" => $this->getDate_amended(),
            "date_deleted" => $this->getDate_deleted(),
            "date_completed" => $this->getDate_completed(),
            "civility" => $this->getCivility(),
            "name" => $this->getName(),
            "firstname" => $this->getFirstname(),
            "ad1" => $this->getAd1(),
            "ad2" => $this->getAd2(),
            "ad3" => $this->getAd3(),
            "city" => $this->getCity(),
            "zipcode" => $this->getZipcode(),
            "email" => $this->getEmail(),
            "tel" => $this->getTel(),
            "amount" => $this->getAmount(),
            "key_edit" => $this->getKey_edit(),
            "group_id" => $this->getGroup_id(),
            "reference" => $this->getReference()
        ];

        return json_encode($aObjet);
    }








    /**
     * Set le champ id
     * @param number $nId nouvelle valeur pour le champ id
     */
    public function setId($nId)
    {
        if( is_null($nId) ) $nId='';
        if( is_numeric($nId)  || $nId=='' )
        {
            $this->nId = $nId;
            $this->aDataSet["id"]=1;
        }
    }



    /**
     * Get le champ id
     * @return number valeur du champ id
     */
    public function getId()
    {
        if( !is_null($this->nId) )
        {
            if( $this->nId==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nId;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nId;
        }
    }



    /**
     * Set le champ date_created
     * @param string $sDate_created nouvelle valeur pour le champ date_created
     */
    public function setDate_created($sDate_created)
    {
        if( is_null($sDate_created) ) $sDate_created='';
        $this->sDate_created = $sDate_created;
        $this->aDataSet["date_created"]=1;
    }



    /**
     * Get le champ date_created
     * @return string valeur du champ date_created
     */
    public function getDate_created()
    {
        if( !is_null($this->sDate_created) )
        {
            if( $this->sDate_created==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_created;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_created'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_created;
        }
    }



    /**
     * Set le champ date_amended
     * @param string $sDate_amended nouvelle valeur pour le champ date_amended
     */
    public function setDate_amended($sDate_amended)
    {
        if( is_null($sDate_amended) ) $sDate_amended='';
        $this->sDate_amended = $sDate_amended;
        $this->aDataSet["date_amended"]=1;
    }



    /**
     * Get le champ date_amended
     * @return string valeur du champ date_amended
     */
    public function getDate_amended()
    {
        if( !is_null($this->sDate_amended) )
        {
            if( $this->sDate_amended==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_amended;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_amended'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_amended;
        }
    }



    /**
     * Set le champ date_deleted
     * @param string $sDate_deleted nouvelle valeur pour le champ date_deleted
     */
    public function setDate_deleted($sDate_deleted)
    {
        if( is_null($sDate_deleted) ) $sDate_deleted='';
        $this->sDate_deleted = $sDate_deleted;
        $this->aDataSet["date_deleted"]=1;
    }



    /**
     * Get le champ date_deleted
     * @return string valeur du champ date_deleted
     */
    public function getDate_deleted()
    {
        if( !is_null($this->sDate_deleted) )
        {
            if( $this->sDate_deleted==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_deleted;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_deleted'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_deleted;
        }
    }



    /**
     * Set le champ date_completed
     * @param string $sDate_completed nouvelle valeur pour le champ date_completed
     */
    public function setDate_completed($sDate_completed)
    {
        if( is_null($sDate_completed) ) $sDate_completed='';
        $this->sDate_completed = $sDate_completed;
        $this->aDataSet["date_completed"]=1;
    }



    /**
     * Get le champ date_completed
     * @return string valeur du champ date_completed
     */
    public function getDate_completed()
    {
        if( !is_null($this->sDate_completed) )
        {
            if( $this->sDate_completed==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_completed;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_completed'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_completed;
        }
    }



    /**
     * Set le champ civility
     * @param string $sCivility nouvelle valeur pour le champ civility
     */
    public function setCivility($sCivility)
    {
        if( is_null($sCivility) ) $sCivility='';
        $this->sCivility = $sCivility;
        $this->aDataSet["civility"]=1;
    }



    /**
     * Get le champ civility
     * @return string valeur du champ civility
     */
    public function getCivility()
    {
        if( !is_null($this->sCivility) )
        {
            if( $this->sCivility==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCivility;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('civility'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCivility;
        }
    }



    /**
     * Set le champ name
     * @param string $sName nouvelle valeur pour le champ name
     */
    public function setName($sName)
    {
        if( is_null($sName) ) $sName='';
        $this->sName = $sName;
        $this->aDataSet["name"]=1;
    }



    /**
     * Get le champ name
     * @return string valeur du champ name
     */
    public function getName()
    {
        if( !is_null($this->sName) )
        {
            if( $this->sName==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sName;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('name'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sName;
        }
    }



    /**
     * Set le champ firstname
     * @param string $sFirstname nouvelle valeur pour le champ firstname
     */
    public function setFirstname($sFirstname)
    {
        if( is_null($sFirstname) ) $sFirstname='';
        $this->sFirstname = $sFirstname;
        $this->aDataSet["firstname"]=1;
    }



    /**
     * Get le champ firstname
     * @return string valeur du champ firstname
     */
    public function getFirstname()
    {
        if( !is_null($this->sFirstname) )
        {
            if( $this->sFirstname==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sFirstname;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('firstname'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sFirstname;
        }
    }



    /**
     * Set le champ ad1
     * @param string $sAd1 nouvelle valeur pour le champ ad1
     */
    public function setAd1($sAd1)
    {
        if( is_null($sAd1) ) $sAd1='';
        $this->sAd1 = $sAd1;
        $this->aDataSet["ad1"]=1;
    }



    /**
     * Get le champ ad1
     * @return string valeur du champ ad1
     */
    public function getAd1()
    {
        if( !is_null($this->sAd1) )
        {
            if( $this->sAd1==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sAd1;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ad1'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sAd1;
        }
    }



    /**
     * Set le champ ad2
     * @param string $sAd2 nouvelle valeur pour le champ ad2
     */
    public function setAd2($sAd2)
    {
        if( is_null($sAd2) ) $sAd2='';
        $this->sAd2 = $sAd2;
        $this->aDataSet["ad2"]=1;
    }



    /**
     * Get le champ ad2
     * @return string valeur du champ ad2
     */
    public function getAd2()
    {
        if( !is_null($this->sAd2) )
        {
            if( $this->sAd2==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sAd2;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ad2'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sAd2;
        }
    }



    /**
     * Set le champ ad3
     * @param string $sAd3 nouvelle valeur pour le champ ad3
     */
    public function setAd3($sAd3)
    {
        if( is_null($sAd3) ) $sAd3='';
        $this->sAd3 = $sAd3;
        $this->aDataSet["ad3"]=1;
    }



    /**
     * Get le champ ad3
     * @return string valeur du champ ad3
     */
    public function getAd3()
    {
        if( !is_null($this->sAd3) )
        {
            if( $this->sAd3==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sAd3;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ad3'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sAd3;
        }
    }



    /**
     * Set le champ city
     * @param string $sCity nouvelle valeur pour le champ city
     */
    public function setCity($sCity)
    {
        if( is_null($sCity) ) $sCity='';
        $this->sCity = $sCity;
        $this->aDataSet["city"]=1;
    }



    /**
     * Get le champ city
     * @return string valeur du champ city
     */
    public function getCity()
    {
        if( !is_null($this->sCity) )
        {
            if( $this->sCity==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCity;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('city'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCity;
        }
    }



    /**
     * Set le champ zipcode
     * @param string $sZipcode nouvelle valeur pour le champ zipcode
     */
    public function setZipcode($sZipcode)
    {
        if( is_null($sZipcode) ) $sZipcode='';
        $this->sZipcode = $sZipcode;
        $this->aDataSet["zipcode"]=1;
    }



    /**
     * Get le champ zipcode
     * @return string valeur du champ zipcode
     */
    public function getZipcode()
    {
        if( !is_null($this->sZipcode) )
        {
            if( $this->sZipcode==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sZipcode;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('zipcode'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sZipcode;
        }
    }



    /**
     * Set le champ email
     * @param string $sEmail nouvelle valeur pour le champ email
     */
    public function setEmail($sEmail)
    {
        if( is_null($sEmail) ) $sEmail='';
        $this->sEmail = $sEmail;
        $this->aDataSet["email"]=1;
    }



    /**
     * Get le champ email
     * @return string valeur du champ email
     */
    public function getEmail()
    {
        if( !is_null($this->sEmail) )
        {
            if( $this->sEmail==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sEmail;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('email'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sEmail;
        }
    }



    /**
     * Set le champ tel
     * @param string $sTel nouvelle valeur pour le champ tel
     */
    public function setTel($sTel)
    {
        if( is_null($sTel) ) $sTel='';
        $this->sTel = $sTel;
        $this->aDataSet["tel"]=1;
    }



    /**
     * Get le champ tel
     * @return string valeur du champ tel
     */
    public function getTel()
    {
        if( !is_null($this->sTel) )
        {
            if( $this->sTel==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sTel;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('tel'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sTel;
        }
    }



    /**
     * Set le champ amount
     * @param numeric $nAmount nouvelle valeur pour le champ amount
     */
    public function setAmount($nAmount)
    {
        if( is_null($nAmount) ) $nAmount='';
        if( is_numeric($nAmount)  || $nAmount=='' )
        {
            $this->nAmount = $nAmount;
            $this->aDataSet["amount"]=1;
        }
    }



    /**
     * Get le champ amount
     * @return numeric valeur du champ amount
     */
    public function getAmount()
    {
        if( !is_null($this->nAmount) )
        {
            if( $this->nAmount==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nAmount;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('amount'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nAmount;
        }
    }



    /**
     * Set le champ key_edit
     * @param number $nKey_edit nouvelle valeur pour le champ key_edit
     */
    public function setKey_edit($nKey_edit)
    {
        if( is_null($nKey_edit) ) $nKey_edit='';
        if( is_numeric($nKey_edit)  || $nKey_edit=='' )
        {
            $this->nKey_edit = $nKey_edit;
            $this->aDataSet["key_edit"]=1;
        }
    }



    /**
     * Get le champ key_edit
     * @return number valeur du champ key_edit
     */
    public function getKey_edit()
    {
        if( !is_null($this->nKey_edit) )
        {
            if( $this->nKey_edit==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nKey_edit;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('key_edit'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nKey_edit;
        }
    }



    /**
     * Set le champ group_id
     * @param number $nGroup_id nouvelle valeur pour le champ group_id
     */
    public function setGroup_id($nGroup_id)
    {
        if( is_null($nGroup_id) ) $nGroup_id='';
        if( is_numeric($nGroup_id)  || $nGroup_id=='' )
        {
            $this->nGroup_id = $nGroup_id;
            $this->aDataSet["group_id"]=1;
        }
    }



    /**
     * Get le champ group_id
     * @return number valeur du champ group_id
     */
    public function getGroup_id()
    {
        if( !is_null($this->nGroup_id) )
        {
            if( $this->nGroup_id==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nGroup_id;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('group_id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nGroup_id;
        }
    }

    public function Group()
    {
        if( $this->nGroup_id){
            $oGroup = new Group(array("id"=>$this->nGroup_id));
            $oGroup->HydrateFromBDD(array("*"));
            return $oGroup;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ reference
     * @param string $sReference nouvelle valeur pour le champ reference
     */
    public function setReference($sReference)
    {
        if( is_null($sReference) ) $sReference='';
        $this->sReference = $sReference;
        $this->aDataSet["reference"]=1;
    }



    /**
     * Get le champ reference
     * @return string valeur du champ reference
     */
    public function getReference()
    {
        if( !is_null($this->sReference) )
        {
            if( $this->sReference==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sReference;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('reference'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sReference;
        }
    }


    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */

    public static function genereRandomReference($nId=0){
        $nTailleMax=8;
        $nTaille=$nTailleMax-strlen($nId)-3;
        if( $nTaille<=2){
            $nTaille=2;
        }
        $sField="".(rand(100000,999999)*5);
        $a=['A','B','C','D','E','F','G','H','J','K','M','N'];
        shuffle($a);

        return $nId.$a[0].$a[1].substr($sField,0,$nTaille).$a[2];
    }
    /*
    ********************************************************************************************
    *                             FIN FONCTIONS PERSONNALISES                     	           *
    ********************************************************************************************
    */


}