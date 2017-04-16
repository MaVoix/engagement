<?php	/**
 * Class Circonscription
 */
class Circonscription	{

    private $aDataNonHydrate = array();
    private $aDataSet = array();
    private $callHydrateFromBDDOnGet = 0;

    private $_sDbInstance = null;

    private $nId;
    private $sDate_created;
    private $sDate_amended;
    private $sDate_deleted;
    private $sCode;
    private $sCode_departement;
    private $nNumber;


    /**
     * Constructeur
     * @param array $aParam tableau de parametres ( clé "id" pour instancier un circonscription avec un id précis )
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
                    //echo "<br />Circonscription->$sMethode() n'existe pas!";
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
            $aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this->getId(),"circonscription",$aFields);

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
        $oReq=DbLink::getInstance($this->_sDbInstance)->prepare('DELETE FROM '."circonscription".' WHERE  id=:id ');
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
        $oReq=DbLink::getInstance($sDbInstance)->prepare('SELECT id FROM '."circonscription".' WHERE  id=:id ');
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

        if(isset($this->aDataSet["code"]))
        {
            $aData["code"]=$this->getCode();
        }

        if(isset($this->aDataSet["code_departement"]))
        {
            $aData["code_departement"]=$this->getCode_departement();
        }

        if(isset($this->aDataSet["number"]))
        {
            $aData["number"]=$this->getNumber();
        }

        if($this->getId()>0)
        {
            DbLink::getInstance($this->_sDbInstance)->update("circonscription",$aData,' id="'.$this->getId().'" ');
        }
        else
        {
            $this->setId(DbLink::getInstance($this->_sDbInstance)->insert("circonscription",$aData));
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
        $this->setCode(NULL);
        $this->setCode_departement(NULL);
        $this->setNumber(NULL);
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
            "code" => $this->getCode(),
            "code_departement" => $this->getCode_departement(),
            "number" => $this->getNumber()
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
     * Set le champ code
     * @param string $sCode nouvelle valeur pour le champ code
     */
    public function setCode($sCode)
    {
        if( is_null($sCode) ) $sCode='';
        $this->sCode = $sCode;
        $this->aDataSet["code"]=1;
    }



    /**
     * Get le champ code
     * @return string valeur du champ code
     */
    public function getCode()
    {
        if( !is_null($this->sCode) )
        {
            if( $this->sCode==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCode;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('code'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCode;
        }
    }



    /**
     * Set le champ code_departement
     * @param string $sCode_departement nouvelle valeur pour le champ code_departement
     */
    public function setCode_departement($sCode_departement)
    {
        if( is_null($sCode_departement) ) $sCode_departement='';
        $this->sCode_departement = $sCode_departement;
        $this->aDataSet["code_departement"]=1;
    }



    /**
     * Get le champ code_departement
     * @return string valeur du champ code_departement
     */
    public function getCode_departement()
    {
        if( !is_null($this->sCode_departement) )
        {
            if( $this->sCode_departement==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCode_departement;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('code_departement'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCode_departement;
        }
    }



    /**
     * Set le champ number
     * @param number $nNumber nouvelle valeur pour le champ number
     */
    public function setNumber($nNumber)
    {
        if( is_null($nNumber) ) $nNumber='';
        if( is_numeric($nNumber)  || $nNumber=='' )
        {
            $this->nNumber = $nNumber;
            $this->aDataSet["number"]=1;
        }
    }



    /**
     * Get le champ number
     * @return number valeur du champ number
     */
    public function getNumber()
    {
        if( !is_null($this->nNumber) )
        {
            if( $this->nNumber==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nNumber;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('number'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nNumber;
        }
    }

    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */


    /*
    ********************************************************************************************
    *                             FIN FONCTIONS PERSONNALISES                     	           *
    ********************************************************************************************
    */


}