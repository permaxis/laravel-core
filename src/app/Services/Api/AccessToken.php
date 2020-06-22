<?php
/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 28/02/2020
 * Time: 16:52
 */

namespace Permaxis\Core\App\Services\Api;


class AccessToken
{
    /**
     * @var boolean
     */
    private $isAdmin;

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param boolean $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }


}