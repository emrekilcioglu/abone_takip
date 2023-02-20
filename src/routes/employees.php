<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//response ve request yapmak için gerekli sınıflar

//require '../vendor/autoload.php'; biz şuanda işlerimizi index içinden yapacağız bu yüzden auto load etmemize burada gerek yok zaten indexte yğklenecekler yüklenecek

$app = new \Slim\App;
//Tüm veriler
$app->get(
    '/employees',
    function (Request $request, Response $response, array $args) {
        //$pattern: codeigniter deki route gibi biz url ye kendi urlmiz + /employees yazarsak bu metod çalışacak. Hatta burası direk route hatta app içinde ki get metodu route interface e ait
        $db = new Db();
        try {
            $db = $db->connect();

            $employees =
                $db->query("select * from employees")->fetchAll(PDO::FETCH_OBJ);

            if (!empty($employees)) {
                $response
                    ->withJson($employees)
                    ->withStatus(200)
                    ->withHeader("Content-Type", "application/json");
                return $response;
            }

            //İf else gerek yok aslında zaten boş veriyse boş dönecek ama ileride bunu kullanabilirsin diye bırakıyorum
    


            /* //*Fetch (getirmek) yaparak verileri getiririz bunu yapmasaydık print de direk sorguyla alakalı veriler gelirdi. Fetch işlemine de parametre vererek bize bu veriyi hangi biçimde getireceğini tanımlarız mesela biz obj dedik yani bize bir çeşit sınıf objesi veriyor,array  var onun altında da sıra sıra bu arrayin elemanları olan sınıflarımız var bu sınıflarda da veriler.
            *Kodlar burada bakmak istersen denersin
            $employees_obj = 
            $db->query("select * from employees")->fetchAll(PDO::FETCH_OBJ);
            
            $employees_default = 
            $db->query("select * from employees")->fetchAll();
            
            echo "Obj <br>";
            print_r($employees_obj);
            echo "Default <br>";
            print_r($employees_default);
            */

        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "erro" => array(
                        "text" => $e->getMessage(),
                        "code" => $e->getCode()
                    )
                )
            );
        }
    }
);

//Belli veriler
$app->get(
    '/employee/{id}' /** {parametre} bu şekilde biz urlden parametre alıyoruz eğer
      *birden fazla parametre almak istiyorsak /employee/{id}/{name}/...  gibi devam etmemiz gerekir */,
    function (Request $request, Response $response, array $args) {
        //$pattern: codeigniter deki route gibi biz url ye kendi urlmiz + /employees yazarsak bu metod çalışacak. Hatta burası direk route
        $id = $request->getAttribute("id");
        //* Biz bu projeye erişmek istediğimizde bir request istek atarız proje de bize bir cevap response cevap verir,vereceğimiz cevapları önceden yaptığımız gibi response da tanımladık ama veri alacaksak bunu bu porjeye istek atandan alacağız,attığı istekden verileri çekeriz bu yüzden $request kullanarak url den gelen veriyi alırız
        $db = new Db();
        try {
            $db = $db->connect();

            $statement = "SELECT * from employees where id = :id";
            $prepare = $db->prepare($statement);
            $prepare->bindParam('id',$id);
            $prepare->execute();

            $employees =
                $prepare->fetchAll(PDO::FETCH_OBJ);

            if (!empty($employees)) {
                $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->withJson($employees);
                return $response;
            } else
                echo " Böyle bir veri yok";

            //İf else gerek yok aslında zaten boş veriyse boş dönecek ama ileride bunu kullanabilirsin diye bırakıyorum
    


            

        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "erro" => array(
                        "text" => $e->getMessage(),
                        "code" => $e->getCode()
                    )
                )
            );
        }
    }
);

// Veri ekleme
//$app->post veya get diyerek bu metodların hangi http metodunda çalışacaklarını tanımlıyoruz
$app->post(
    '/employee/add',
    function (Request $request, Response $response, array $args) {
        //$pattern: codeigniter deki route gibi biz url ye kendi urlmiz + /employees yazarsak bu metod çalışacak. Hatta burası direk route
        $name = $request->getParam("name");
        $surname = $request->getParam("surname");
        $phone_number = $request->getParam("phone_number");
        $tc_no = $request->getParam("tc_no");


        //Get param metodu ile post ile bize gelen verileri alırız,mesela ben json verileri gönderdim bunlarında keyleri ile rahatça verilere eriştik
    

        //* Biz bu projeye erişmek istediğimizde bir request istek atarız proje de bize bir cevap response cevap verir,vereceğimiz cevapları önceden yaptığımız gibi response da tanımladık ama veri alacaksak bunu bu porjeye istek atandan alacağız,attığı istekden verileri çekeriz bu yüzden $request kullanarak url den gelen veriyi alırız
    
        $db = new Db();
        try {
            $db = $db->connect();

            $statement /**Bunu kelimeyi kaydet */= "INSERT INTO employees (name,surname,phone_number, tc_no) VALUES (:name,:surname,:phone_number,:tc_no)";
            //*! Verileri direk $ diyerek yapıştırmak yerine pdo da :değişken adı şeklinde verileri alırsak gelecek zararlı istekleri gelen verileri engeller,mesela adam veri yerine bir sql sorgusu atarsa veri tabanı patlayabilir bu olay bunu engeller
    
            $prepare = $db->prepare($statement); //prepare anlamına bak zaten anlarsın
    


            $prepare->bindParam('name', $name, PDO::PARAM_STR);
            $prepare->bindParam('surname', $surname, PDO::PARAM_STR);
            $prepare->bindParam('phone_number', $phone_number, PDO::PARAM_STR);
            $prepare->bindParam('tc_no', $tc_no, PDO::PARAM_STR);






            /**Yine bind kelimesini öğren anlarsın ,bu 
            bize veri diye sql sorgusu atıp patlatmasınlar diye tanımladığımız şeylere veri atmak için
            kullanacağız */
            $employee = $prepare->execute(); //execute zaten
    
            if ($employee) {
                $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->withJson(
                        array(
                            "text" => "employee başarılı bir şekilde eklendi"
                        )
                    );
                return $response;


            } else {
                $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->withJson(
                        array(
                            "error" => array(
                                "text" => "Ekleme işlemi sırasında bir hata oluştu"
                            )
                        )
                    );
                return $response;
            }







            //İf else gerek yok aslında zaten boş veriyse boş dönecek ama ileride bunu kullanabilirsin diye bırakıyorum
    


            /* //*Fetch (getirmek) yaparak verileri getiririz bunu yapmasaydık print de direk sorguyla alakalı veriler gelirdi. Fetch işlemine de parametre vererek bize bu veriyi hangi biçimde getireceğini tanımlarız mesela biz obj dedik yani bize bir çeşit sınıf objesi veriyor,array  var onun altında da sıra sıra bu arrayin elemanları olan sınıflarımız var bu sınıflarda da veriler.
            *Kodlar burada bakmak istersen denersin
            $employees_obj = 
            $db->query("select * from employees")->fetchAll(PDO::FETCH_OBJ);
            
            $employees_default = 
            $db->query("select * from employees")->fetchAll();
            
            echo "Obj <br>";
            print_r($employees_obj);
            echo "Default <br>";
            print_r($employees_default);
            */

        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "erro" => array(
                        "text" => $e->getMessage(),
                        "code" => $e->getCode()
                    )
                )
            )->withStatus(500);
            //! Response da header ve codeler son parametre olarak
        }
    }
);

$app->put(
    '/employee/update/{id}',
    function (Request $request, Response $response, array $args) {
        //$pattern: codeigniter deki route gibi biz url ye kendi urlmiz + /employees yazarsak bu metod çalışacak. Hatta burası direk route
    
        $id = $request->getAttribute("id"); /** Url ile gelenleri attribute ile,direk veri olarak 
            *gelenleri ise param ile alırız  */
        if ($id) {
            $name = $request->getParam("name");
            $surname = $request->getParam("surname");
            $phone_number = $request->getParam("phone_number");
            $tc_no = $request->getParam("tc_no");
            


            $db = new Db();
            try {
                $db = $db->connect();

                $statement /**Bunu kelimeyi kaydet */= "UPDATE employees SET name = :name,
                 surname= :surname, phone_number= :phone_number ,tc_no= :tc_no WHERE id= :id";
                //*! Verileri direk $ diyerek yapıştırmak yerine pdo da :değişken adı şeklinde verileri alırsak gelecek zararlı istekleri gelen verileri engeller,mesela adam veri yerine bir sql sorgusu atarsa veri tabanı patlayabilir bu olay bunu engeller
    
                $prepare = $db->prepare($statement); //prepare anlamına bak zaten anlarsın
    

                $prepare->bindParam('id', $id, PDO::PARAM_STR);
                $prepare->bindParam('name', $name, PDO::PARAM_STR);
                $prepare->bindParam('surname', $surname, PDO::PARAM_STR);
                $prepare->bindParam('phone_number', $phone_number, PDO::PARAM_STR);
                $prepare->bindParam('tc_no', $tc_no, PDO::PARAM_STR);







                /**Yine bind kelimesini öğren anlarsın ,bu 
                bize veri diye sql sorgusu atıp patlatmasınlar diye tanımladığımız şeylere veri atmak için
                kullanacağız */
                $employee = $prepare->execute(); //execute zaten
    
                if ($employee) {
                    $response
                        ->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->withJson(
                            array(
                                "text" => "employee başarılı bir şekilde güncellendi"
                            )
                        );
                    return $response;


                } else {
                    $response
                        ->withStatus(500)
                        ->withHeader("Content-Type", "application/json")
                        ->withJson(
                            array(
                                "error" => array(
                                    "text" => "Eksik veri"
                                )
                            )
                        );
                    return $response;
                }







                //İf else gerek yok aslında zaten boş veriyse boş dönecek ama ileride bunu kullanabilirsin diye bırakıyorum
    


                /* //*Fetch (getirmek) yaparak verileri getiririz bunu yapmasaydık print de direk sorguyla alakalı veriler gelirdi. Fetch işlemine de parametre vererek bize bu veriyi hangi biçimde getireceğini tanımlarız mesela biz obj dedik yani bize bir çeşit sınıf objesi veriyor,array  var onun altında da sıra sıra bu arrayin elemanları olan sınıflarımız var bu sınıflarda da veriler.
                *Kodlar burada bakmak istersen denersin
                $employees_obj = 
                $db->query("select * from employees")->fetchAll(PDO::FETCH_OBJ);
                
                $employees_default = 
                $db->query("select * from employees")->fetchAll();
                
                echo "Obj <br>";
                print_r($employees_obj);
                echo "Default <br>";
                print_r($employees_default);
                */

            } catch (PDOException $e) {
                return $response->withJson(
                    array(
                        "erro" => array(
                            "text" => $e->getMessage(),
                            "code" => $e->getCode()
                        )
                    )
                )->withStatus(500);
                //! Response da header ve codeler son parametre olarak
            }
            $db = null;

        } else {
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => "ID bilgisi eksik"
                    )
                )
            );
        }



        //Get param metodu ile post ile bize gelen verileri alırız,mesela ben json verileri gönderdim bunlarında keyleri ile rahatça verilere eriştik
    

        //* Biz bu projeye erişmek istediğimizde bir request istek atarız proje de bize bir cevap response cevap verir,vereceğimiz cevapları önceden yaptığımız gibi response da tanımladık ama veri alacaksak bunu bu porjeye istek atandan alacağız,attığı istekden verileri çekeriz bu yüzden $request kullanarak url den gelen veriyi alırız
    
    }
);

//Belli veriler
$app->delete(
    '/employee/{id}' /** {parametre} bu şekilde biz urlden parametre alıyoruz eğer
      *birden fazla parametre almak istiyorsak /employee/{id}/{name}/...  gibi devam etmemiz gerekir */,
    function (Request $request, Response $response, array $args) {
        //$pattern: codeigniter deki route gibi biz url ye kendi urlmiz + /employees yazarsak bu metod çalışacak. Hatta burası direk route
        $id = $request->getAttribute("id");
        //* Biz bu projeye erişmek istediğimizde bir request istek atarız proje de bize bir cevap response cevap verir,vereceğimiz cevapları önceden yaptığımız gibi response da tanımladık ama veri alacaksak bunu bu porjeye istek atandan alacağız,attığı istekden verileri çekeriz bu yüzden $request kullanarak url den gelen veriyi alırız
        $db = new Db();
        try {
            $db = $db->connect();

            $statement = "DELETE from employees where id = :id";
            $prepare = $db->prepare($statement);
            $prepare->bindParam('id',$id);
            $employees =$prepare->execute();
                

            if ($employees) {
                $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->withJson(array(
                        "text" => "Çalışan başarılı bir şekilde silinmiştir"
                    ));
                return $response;
            } else
                echo "Çalışanı silerken hata oluştu";

            //İf else gerek yok aslında zaten boş veriyse boş dönecek ama ileride bunu kullanabilirsin diye bırakıyorum
    


            

        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "erro" => array(
                        "text" => $e->getMessage(),
                        "code" => $e->getCode()
                    )
                )
            );
        }
    }
);
