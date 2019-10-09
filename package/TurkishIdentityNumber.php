<?php
namespace NVI;


final class TurkishIdentityNumber
{
    /** @var string */
    protected $identityNumber;

    /** @var string */
    protected $name;

    /** @var string */
    protected $surname;

    /** @var integer */
    protected $birthDay;

    /**
     * TurkishIdentityNumber constructor.
     * @param string $identityNumber
     * @param string $name
     * @param string $surname
     * @param int $birthDay
     */
    public function __construct(string $identityNumber, string $name, string $surname, int $birthDay)
    {
        $this->identityNumber = $identityNumber;
        $this->name = mb_convert_case($name, MB_CASE_UPPER, "UTF-8");
        $this->surname = mb_convert_case($surname, MB_CASE_UPPER, "UTF-8");
        $this->birthDay = $birthDay;
    }


    /**
     * @return bool
     * @throws \SoapFault
     */
    public function verify()
    {
        $postData = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
					<TCKimlikNo>'.$this->identityNumber.'</TCKimlikNo>
					<Ad>'.$this->name.'</Ad>
					<Soyad>'.$this->surname.'</Soyad>
					<DogumYili>'.$this->birthDay.'</DogumYili>
				</TCKimlikNoDogrula>
			</soap:Body>
		</soap:Envelope>';

        $ch = curl_init();
        // CURL options
        $options = array(
            CURLOPT_URL                => 'https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx',
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS        => $postData,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_SSL_VERIFYPEER    => false,
            CURLOPT_HEADER            => false,
            CURLOPT_HTTPHEADER        => array(
                'POST /Service/KPSPublic.asmx HTTP/1.1',
                'Host: tckimlik.nvi.gov.tr',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
                'Content-Length: '.strlen($postData)
            ),
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);



        return strpos($response, 'true') !== false;

    }


}