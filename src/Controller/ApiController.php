<?php
namespace App\Controller;

use App\Entity\Region;
use App\Repository\RegionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    //recup regions from api and adding them in the db
    /**
     * @Route("/api/regions/api", name="apiContain_adding",methods={"GET"})
     */
    public function addRegionByApi(SerializerInterface $serialiser)
    {
        $regionJson = file_get_contents("https://geo.api.gouv.fr/regions") ; 
        /*
        //methode1 => json en object
         //decode json vers tableau
        $regionTab=$serialiser->decode($regionJson,"json") ;
        //dd($regionTab) ;
        //we denormalze table vers oobject
        $regionObject=$serialiser->denormalize($regionTab, 'App\Entity\Region[]') ;
        //dd($regionObject) ; */

        //methode2 deserialisation json vers object ou tableau d'object
        $regionObject = $serialiser->deserialize($regionJson,'App\Entity\Region[]','json') ;
       
        //put data deserialised received in the database
        $entityManager = $this->getDoctrine()->getManager() ;
        foreach($regionObject as $region){

            $entityManager->persist($region) ;

        }
        $entityManager->flush() ; 

        return new JsonResponse("succes",Response::HTTP_CREATED,[],true) ;

    }

    //show data from database 
     /**
     * @Route("/api/regions", name="lists_region",methods={"GET"})
     */  
    public function showRegion(SerializerInterface $serialiser,RegionRepository $repo){
        $regionObject=$repo->findAll() ; //from db
    
        $regionJson= $serialiser->serialize($regionObject,"json",
        [
            "groups"=>["region:read_all"]
        ]
        ) ;  
        //for can reading by customer
        return new JsonResponse($regionJson,Response::HTTP_OK,[],true) ;
    }

     //add region
    /**
     * @Route("/api/regions", name="add_new_region",methods={"POST"})
     */  
    public function addRegion(SerializerInterface $serialiser,Request $request ,ValidatorInterface $validator){
        
        //recup contain recup body from customer
        
         $regionJson = $request->getContent(); 
         //deserialize it for adding like object 
         $region = $serialiser->deserialize($regionJson,Region::class,"json") ;
         //validation
         $errors = $validator->validate($region);
         if (count($errors)>0) { $errorsString =$serialiser->serialize($errors,"json");
                return new JsonResponse( $errorsString ,Response::HTTP_BAD_REQUEST,[],true);     
         }

         $entityManager=$this->getDoctrine()->getManager() ;
         $entityManager->persist($region) ;
         $entityManager->flush();
         return new JsonResponse("succes",Response::HTTP_CREATED,[],true) ;
         
    }
 
}
