<?php
class Scookie{
    private $salt = 'stourwebcms';
    private $path = '/';
    private $domain = '';
    private $db = NULL;
    private $expiration = 0;
    private $secure = FALSE;
    private $httponly = FALSE;

    public function __construct($db)
    {
        if($db)
        {
            $this->db = $db;
        }
        else
        {
            include_once("db.php");
            $this->db = new DB();
        }
        $this->domain = $this->cookie_domain();
    }
    public  function cookie_domain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $sql = "SELECT * FROM `sline_weblist` WHERE webid=0";
        $arr = $this->db->get_one($sql);
        if (!empty($arr))
        {
            $host = str_replace($arr['webprefix'] . '.', '', parse_url($arr['weburl'], PHP_URL_HOST));
        }
        return $host;
    }

    public  function set($name, $value, $expiration = NULL)
    {
        if ($expiration === NULL)
        {
            // Use the default expiration
            $expiration = $this->expiration;
        }

        if ($expiration !== 0)
        {
            // The expiration is expected to be a UNIX timestamp
            $expiration += time();
        }

        // Add the salt to the cookie value
        $value = $this->salt($name, $value).'~'.$value;

        return setcookie($name, $value, $expiration, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    /**
     * Deletes a cookie by making the value NULL and expiring it.
     * @param   string  $name   cookie name
     * @return  boolean
     * @uses    Cookie::set
     */
    public  function delete($name)
    {
        // Remove the cookie
        unset($_COOKIE[$name]);

        // Nullify the cookie and make it expire
        return setcookie($name, NULL, -86400, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    /**
     * Generates a salt string for a cookie based on the name and value.
     *
     *
     * @param   string  $name   name of cookie
     * @param   string  $value  value of cookie
     * @return  string
     */
    public  function salt($name, $value)
    {

        // Determine the user agent
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

        return sha1($agent.$name.$value.$this->salt);
    }
}