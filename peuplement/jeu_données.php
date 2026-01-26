<?php
/*
Fichier générant les données dans la base de données

Le fichier doit être executé dans le containeur web (avec 'docker exec web php ../peuplement/jeu_données.php' par exemple)

Le fichier doit se trouver dans un dossier à côté de html (obligatoire)

Le fichier doit (si possible) se trouver au dessus d'un dossier "ressources_temp",
avec dedans un dossier "produit",
avec dedans toutes les images nommées "id_image.png"
avec id_image mise dans l'array du produit

exemple d'archi :

|
|
|- html
|
|- peuplement
	|
	|- jeu_données.php
	|
	|- ressources_temp
		|
		|- produit
			|
			|- 1.png
			|
			|- 2.png
|
|- .config.php
|
.
.
.

*/


define("HOME_GIT", '../');

require __DIR__ . "/../.config.php";
require __DIR__ . "/../fonction_compte.php";
require __DIR__ . "/../fonction_produit.php";
require __DIR__ . "/../fonction_avis.php";


function vider_tables() {
	global $pdo;

	$tables = [
		'_admin',
		'_cle_vendeur',
		'_elt_commande',
		'_commande',
		'_elt_panier',
		'_images_produit',
		'_promotion',
		'_signalement',
		'_reponse',
		'_avis',
		'_produit',
		'_client',
		'_vendeur',
		'_compte',
		'_question_secu',
		'_adresse',
		'_image',
	];

	foreach ($tables as $t) {
		$pdo->query("DELETE FROM $t");
	}
	
	$pdo->query("DELETE FROM _categorie WHERE nom_categorie_sup IS NOT NULL");
	$pdo->query("DELETE FROM _categorie");
}

function i_question_secu() {
	global $pdo;

	$questions = [
		'mere' => 'Quel était le nom de jeune fille de votre mère ?',
		'animal' => 'Quel était le nom de votre premier animal de compagnie ?',
		'professeur' => 'Quel était le nom de votre professeur préféré ?'
	];

	foreach($questions as $q => $nom_q) {
		$pdo->query("INSERT INTO _question_secu VALUES ('$q', '$nom_q')");
	}
}

function i_categories() {
	global $pdo;

	$categories = [
		'Alimentaire' => NULL,
		'Sucré' => 'Alimentaire',
		'Salé'=> 'Alimentaire',
		'Boisson'=> 'Alimentaire',

		'Électroménager'=> NULL,
		'Électronique'=> NULL,
		'Soin & Hygiène'=> NULL,
		'Vêtements'=> NULL,
		'Loisirs' => NULL,
		'Jeux' => 'Loisirs',
		'Sports' => 'Loisirs',
		'Livres' => 'Loisirs',

		'Entretien' => NULL,
		'Décoration' => NULL,
	];

	foreach($categories as $cat => $parent) {
		if (isset($parent)) {
			$pdo->query("INSERT INTO _categorie VALUES ('$cat', '$parent')");
		} else {
			$pdo->query("INSERT INTO _categorie VALUES ('$cat', NULL)");
		}
	}
}

function i_client() {
	global $pdo;

	$clients = [
			[
				'email' => 'didi@gmail.com',
				'mdp' => 'Mo3passTropcool', 
				'pseudo' => 'didi',
				'prenom' => 'didi',
				'nom' => 'lacheteur',
				'date_naissance' =>'2000-01-01',
				'mot_clef' => 'mere',
				'reponse' => 'arkenive',
				'ville' => 'Lannion',
				'adresse' => '25 rue de la liberté',
				'code_postal' => '22530',
				'complement_adresse' => ''
			],


			[
				'email' => 'dudu@gmail.com',
				'mdp' => 'Mo4passTropcool', 
				'pseudo' => 'dudu',
				'prenom' => 'dudu',
				'nom' => 'lobteneur',
				'date_naissance' =>'1900-04-05', 
				'mot_clef' => 'mere',
				'reponse' => 'arkenive',
				'ville' => 'Lannion',
				'adresse' => '26 rue de la liberté',
				'code_postal' => '22530',
				'complement_adresse' => ''
			],

			[
				'email' => 'edlelay@gmail.com',
				'mdp' =>  'J\'4D043_L3_M3T4L', 
				'pseudo' => 'arkenive',
				'prenom' =>  'Eouar',
				'nom' => 'Récupérateur',
				'date_naissance' => '2012-02-05', 
				'mot_clef' => 'mere',
				'reponse' => 'arkenive',
				'ville' => 'Lannion',
				'adresse' => '2 rue de la Vérité et de la Falsification',
				'code_postal' => '22530',
				'complement_adresse' => ''
			],

			[
				'email' => 'commentateur@gmail.com',
				'mdp' =>  'JeSuisUnCommentateur', 
				'pseudo' => 'Commentateur',
				'prenom' =>  'Comment',
				'nom' => 'Ateur',
				'date_naissance' => '2020-01-10', 
				'mot_clef' => 'mere',
				'reponse' => 'arkenive',
				'ville' => 'Lannion',
				'adresse' => '28 rue de la liberté',
				'code_postal' => '22530',
				'complement_adresse' => ''
			]
		];
	
	foreach($clients as $client) {
		sql_create_client($pdo, $client['nom'], $client['prenom'], $client['pseudo'], $client['email'], $client['date_naissance'], $client['mdp'], $client['mot_clef'], $client['reponse']);

		$id_compte = $pdo->lastInsertId();

		sql_insert_adresse_client($pdo, $id_compte, $client['ville'], $client['adresse'], $client['complement_adresse'], $client['code_postal']);
	}
		
}

function i_vendeur() {
	global $pdo;

	$vendeurs = [
		[
			'email' => 'dede@gmail.com',
			'mdp' => 'Mo2passTropcool',
			'raison_sociale' => 'dédélevendeur', 
			'num_siret' => '2848',
			'ville' => 'Lannion',
			'adresse' => '32 rue de la liberté',
			'code_postal' => '22530',
			'complement_adresse' => ''
		],

		[
			'email' => 'th@thusaccin.com',
			'mdp' => 'M0n_VR41_M0T_D3_P4553',
			'raison_sociale' => 'ThusaCorp', 
			'num_siret' => '24896',
			'ville' => 'Lannion',
			'adresse' => '33 rue de la liberté',
			'code_postal' => '22530',
			'complement_adresse' => ''
		],

		[
			'email' => 'ytanneau@gmail.com',
			'mdp' => 'hee33u_jesaispas',
			'raison_sociale' => 'YannTentreprise', 
			'num_siret' => '156385',
			'ville' => 'Lannion',
			'adresse' => '34 rue de la liberté',
			'code_postal' => '22530',
			'complement_adresse' => ''
		],

		[
			'email' => 'ivillalard@gmail.com',
			'mdp' => 'zzzzzzzzzzza',
			'raison_sociale' => 'VillSociety', 
			'num_siret' => '25852587',
			'ville' => 'Lannion',
			'adresse' => '35 rue de la liberté',
			'code_postal' => '22530',
			'complement_adresse' => ''
		]
	];

	
	
	foreach($vendeurs as $vendeur) {
		$pdo->query('INSERT INTO _cle_vendeur VALUES (\'AAAAAAAAAAAAAAA\')');
		sql_create_vendeur($pdo, $vendeur['raison_sociale'], $vendeur['num_siret'], $vendeur['email'], $vendeur['ville'], $vendeur['adresse'], $vendeur['complement_adresse'], $vendeur['code_postal'], $vendeur['mdp'], 'AAAAAAAAAAAAAAA');
	}
}


function i_produit() {
	global $pdo;

	$produits = [
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Kouign Amann',
			'nom_public' => 'Kouign Amann 15g',
			'description' => 'Kouign Amann au beuur',
			'description_detaillee' => 'Kouign Amann au beuur saléééé',
			'code_barre' => '3256987451211',
			'quantite' => '15',
			'prix' => '19.99',
			'seuil_alerte' => '10',
			'poids' => '15',
			'volume' => '50',
			'categorie' => 'Sucré',
			'num_image' => '1',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Galettes blé noir',
			'nom_public' => 'Galettes de blé noir',
			'description' => 'Galettes traditionnelles au sarrasin breton',
			'description_detaillee' => 'Galettes fines préparées avec de la farine de sarrasin bretonne, sans gluten, idéales pour accompagner jambon, œuf ou fromage. Authentique recette artisanale.',
			'code_barre' => '3256987451210',
			'quantite' => '30',
			'prix' => '4.5',
			'seuil_alerte' => '8',
			'poids' => '500',
			'volume' => '600',
			'categorie' => 'Salé',
			'num_image' => '2',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Far breton pruneaux',
			'nom_public' => 'Far breton aux pruneaux',
			'description' => 'Dessert typique moelleux et gourmand',
			'description_detaillee' => 'Far traditionnel préparé avec des œufs frais, du lait entier et des pruneaux charnus. Saveur douce rappelant les goûters bretons d’antan.',
			'code_barre' => '3256987451234',
			'quantite' => '25',
			'prix' => '4.2',
			'seuil_alerte' => '5',
			'poids' => '400',
			'volume' => '350',
			'categorie' => 'Sucré',
			'num_image' => '3',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Beurre baratte',
			'nom_public' => 'Beurre demi-sel de baratte',
			'description' => 'Beurre artisanal riche et onctueux',
			'description_detaillee' => 'Ce beurre est baratté lentement à partir de crème maturée, puis salé au sel de Guérande. Idéal pour la cuisine et les tartines.',
			'code_barre' => '3256987451241',
			'quantite' => '40',
			'prix' => '2.8',
			'seuil_alerte' => NULL,
			'poids' => '250',
			'volume' => '200',
			'categorie' => 'Alimentaire',
			'num_image' => '4',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Caramel beurre salé',
			'nom_public' => 'Caramel au beurre salé',
			'description' => 'Gourmandise bretonne douce et fondante',
			'description_detaillee' => 'Caramel cuisiné à partir de sucre, beurre demi-sel et crème fraîche. Texture lisse et goût équilibré entre sucré et salé. À tartiner ou pour pâtisserie.',
			'code_barre' => '3256987451258',
			'quantite' => '50',
			'prix' => '5.6',
			'seuil_alerte' => '10',
			'poids' => '220',
			'volume' => '180',
			'categorie' => 'Alimentaire',
			'num_image' => '5',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Palets breton',
			'nom_public' => 'Palets bretons pur beurre',
			'description' => 'Biscuits sablés croquants et dorés',
			'description_detaillee' => 'Recette traditionnelle à base de beurre salé et de farine de blé. Texture sablée et goût authentique, parfaits avec le café ou le thé.',
			'code_barre' => '3256987451265',
			'quantite' => '60',
			'prix' => '3.5',
			'seuil_alerte' => NULL,
			'poids' => '150',
			'volume' => '100',
			'categorie' => 'Sucré',
			'num_image' => '6',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Soupe poissons',
			'nom_public' => 'Soupe de poissons bretonne',
			'description' => 'Soupe riche en saveurs marines',
			'description_detaillee' => 'Préparée avec poissons frais de l’Atlantique, légumes et épices. Servir chaude avec croûtons et rouille. Recette artisanale issue des ports bretons.',
			'code_barre' => '3256987451272',
			'quantite' => '20',
			'prix' => '7.9',
			'seuil_alerte' => '4',
			'poids' => '950',
			'volume' => '750',
			'categorie' => 'Salé',
			'num_image' => '7',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Rillettes maquereau',
			'nom_public' => 'Rillettes de maquereau au cidre',
			'description' => 'Tartinade marine typique',
			'description_detaillee' => 'Rillettes artisanales élaborées avec maquereaux pêchés au large de Quiberon, relevées d’une touche de cidre brut breton. Texture fine et goût iodé.',
			'code_barre' => '3256987451289',
			'quantite' => '35',
			'prix' => '4.7',
			'seuil_alerte' => '7',
			'poids' => '180',
			'volume' => '150',
			'categorie' => 'Salé',
			'num_image' => '8',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Crêpes dentelle',
			'nom_public' => 'Crêpes dentelle de Bretagne',
			'description' => 'Biscuits fins et croustillants',
			'description_detaillee' => 'Crêpes délicatement roulées et dorées au four, élaborées à partir d’une pâte légère. Idéales avec chocolat fondu, fruits ou glace.',
			'code_barre' => '3256987451296',
			'quantite' => '45',
			'prix' => '3.8',
			'seuil_alerte' => '10',
			'poids' => '125',
			'volume' => '100',
			'categorie' => 'Sucré',
			'num_image' => '9',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Smartphone ArmorX5',
			'nom_public' => 'Smartphone ArmorX5',
			'description' => 'Téléphone robuste tout-terrain étanche',
			'description_detaillee' => 'Le ArmorX5 est un smartphone résistant à l’eau, à la poussière et aux chocs, équipé d’un écran 5.5 HD, d’une batterie longue durée de 5000 mAh et d’un processeur octa-core pour un usage professionnel ou en extérieur.',
			'code_barre' => '4512369874102',
			'quantite' => '25',
			'prix' => '199.9',
			'seuil_alerte' => NULL,
			'poids' => '230',
			'volume' => '380',
			'categorie' => 'Électronique',
			'num_image' => '10',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Casque audio BreizhSound Pro',
			'nom_public' => 'Casque audio BreizhSound Pro',
			'description' => 'Casque sans fil à réduction de bruit',
			'description_detaillee' => 'Ce casque Bluetooth offre une qualité audio haute fidélité avec réduction active du bruit, autonomie de 30 heures et coussinets à mémoire de forme. Compatible avec tous les smartphones et ordinateurs.',
			'code_barre' => '4512369874119',
			'quantite' => '40',
			'prix' => '129.0',
			'seuil_alerte' => NULL,
			'poids' => '320',
			'volume' => '900',
			'categorie' => 'Électronique',
			'num_image' => '11',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Montre connectée CeltWatch S',
			'nom_public' => 'Montre connectée CeltWatch S',
			'description' => 'Montre connectée étanche et élégante',
			'description_detaillee' => 'Montre intelligente avec suivi de la fréquence cardiaque, GPS, notifications d’appels et d’applications. Boîtier en acier inoxydable et bracelet en silicone. Étanche jusqu’à 50 m.',
			'code_barre' => '4512369874126',
			'quantite' => '30',
			'prix' => '89.9',
			'seuil_alerte' => '6',
			'poids' => '80',
			'volume' => '120',
			'categorie' => 'Électronique',
			'num_image' => '12',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Ordinateur portable ArmorBook 15',
			'nom_public' => 'Ordinateur portable ArmorBook 15',
			'description' => 'PC portable professionnel 15 pouces',
			'description_detaillee' => 'Ordinateur équipé d’un processeur Intel i7, 16 Go de RAM, SSD 512 Go et écran Full HD. Idéal pour le télétravail, la bureautique et la création graphique légère. Châssis en aluminium.',
			'code_barre' => '4512369874133',
			'quantite' => '10',
			'prix' => '899.0',
			'seuil_alerte' => '2',
			'poids' => '1800',
			'volume' => '2200',
			'categorie' => 'Électronique',
			'num_image' => '13',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Enceinte Bluetooth BreizhBass',
			'nom_public' => 'Enceinte Bluetooth BreizhBass',
			'description' => 'Enceinte portable puissante et étanche',
			'description_detaillee' => 'Enceinte sans fil au son clair et profond, résistance IP67, autonomie 20 h. Idéale pour la plage ou les pique-niques. Recharge rapide par USB-C.',
			'code_barre' => '4512369874140',
			'quantite' => '50',
			'prix' => '79.9',
			'seuil_alerte' => '10',
			'poids' => '550',
			'volume' => '500',
			'categorie' => 'Électronique',
			'num_image' => '14',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Appareil photo ArmorCam ZR100',
			'nom_public' => 'Appareil photo ArmorCam ZR100',
			'description' => 'Appareil photo numérique compact',
			'description_detaillee' => 'Appareil 24 MP avec zoom optique 10x, écran orientable et stabilisation d’image. Parfait pour la photo de voyage et les vlogs. Enregistrement vidéo Full HD.',
			'code_barre' => '4512369874157',
			'quantite' => '15',
			'prix' => '259.0',
			'seuil_alerte' => '3',
			'poids' => '420',
			'volume' => '350',
			'categorie' => 'Électronique',
			'num_image' => '15',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Tablette BreizhTab 10',
			'nom_public' => 'Tablette BreizhTab 10',
			'description' => 'Tablette tactile 10 pouces performante',
			'description_detaillee' => 'Écran IPS 10, processeur octa-core, 8 Go de RAM et 128 Go de stockage. Compatible 4G et Wi-Fi. Autonomie jusqu’à 12 h. Idéale pour films, jeux et travail nomade.',
			'code_barre' => '4512369874164',
			'quantite' => '20',
			'prix' => '299.0',
			'seuil_alerte' => '5',
			'poids' => '520',
			'volume' => '600',
			'categorie' => 'Électronique',
			'num_image' => '16',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Clavier mécanique ArmorKey RGB',
			'nom_public' => 'Clavier mécanique ArmorKey RGB',
			'description' => 'Clavier de jeu rétroéclairé RGB',
			'description_detaillee' => 'Clavier mécanique à switches rouges pour une frappe fluide et rapide. Rétroéclairage personnalisable et construction robuste. Parfait pour gamers et développeurs.',
			'code_barre' => '4512369874171',
			'quantite' => '60',
			'prix' => '89.0',
			'seuil_alerte' => '10',
			'poids' => '950',
			'volume' => '800',
			'categorie' => 'Électronique',
			'num_image' => '17',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Souris sans fil BreizhMouse X',
			'nom_public' => 'Souris sans fil BreizhMouse X',
			'description' => 'Souris ergonomique rechargeable',
			'description_detaillee' => 'Souris Bluetooth silencieuse avec capteur optique haute précision, autonomie de 60 jours, port USB-C et design confortable pour droitiers et gauchers.',
			'code_barre' => '4512369874188',
			'quantite' => '80',
			'prix' => '39.9',
			'seuil_alerte' => '15',
			'poids' => '120',
			'volume' => '90',
			'categorie' => 'Électronique',
			'num_image' => '18',
	
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Écouteurs ArmorPods Air+',
			'nom_public' => 'Écouteurs ArmorPods Air+',
			'description' => 'Écouteurs Bluetooth avec boîtier de charge',
			'description_detaillee' => 'Écouteurs intra-auriculaires à réduction de bruit, autonomie 5 h + 20 h avec boîtier. Son clair et basses puissantes. Connexion instantanée avec tous les appareils.',
			'code_barre' => '4512369874195',
			'quantite' => '100',
			'prix' => '69.9',
			'seuil_alerte' => '20',
			'poids' => '60',
			'volume' => '70',
			'categorie' => 'Électronique',
			'num_image' => '19',
	
		],
		[
			'email_vendeur' => 'ytanneau@gmail.com',
			'nom_stock' => 'Savon lait brebis',
			'nom_public' => 'Savon au lait de brebis',
			'description' => 'Savon naturel hydratant et doux',
			'description_detaillee' => 'Savon artisanal fabriqué en Bretagne à base de lait de brebis local, d’huiles végétales et d’huiles essentielles. Convient aux peaux sensibles et sèches. Sans parabène ni colorant.',
			'code_barre' => '4897523014102',
			'quantite' => '40',
			'prix' => '6.5',
			'seuil_alerte' => '8',
			'poids' => '100',
			'volume' => '90',
			'categorie' => 'Soin & Hygiène',
			'num_image' => '20',
	
		],
		[
			'email_vendeur' => 'ytanneau@gmail.com',
			'nom_stock' => 'Crème miel sarrasin',
			'nom_public' => 'Crème mains au miel de sarrasin',
			'description' => 'Crème nourrissante et réparatrice',
			'description_detaillee' => 'Crème onctueuse enrichie en miel de sarrasin breton et en beurre de karité. Hydrate durablement les mains sèches et laisse une odeur douce et sucrée. Fabriquée en Bretagne.',
			'code_barre' => '4897523014119',
			'quantite' => '30',
			'prix' => '9.9',
			'seuil_alerte' => NULL,
			'poids' => '75',
			'volume' => '80',
			'categorie' => 'Soin & Hygiène',
			'num_image' => '21',
	
		],
		[
			'email_vendeur' => 'ytanneau@gmail.com',
			'nom_stock' => 'Shampoing algues',
			'nom_public' => 'Shampoing solide aux algues',
			'description' => 'Shampoing écologique et revitalisant',
			'description_detaillee' => 'Formule naturelle à base d’algues marines de Roscoff, nourrissant les cheveux et le cuir chevelu. Remplace deux bouteilles de shampoing liquide. Sans sulfate, 100% biodégradable.',
			'code_barre' => '4897523014126',
			'quantite' => '50',
			'prix' => '8.2',
			'seuil_alerte' => '10',
			'poids' => '85',
			'volume' => '70',
			'categorie' => 'Soin & Hygiène',
			'num_image' => '22',
	
		],
		[
			'email_vendeur' => 'ytanneau@gmail.com',
			'nom_stock' => 'Baume beurre salé',
			'nom_public' => 'Baume à lèvres au beurre salé',
			'description' => 'Baume protecteur au goût breton',
			'description_detaillee' => 'Baume à lèvres original au beurre demi-sel et à la cire d’abeille. Protège et adoucit les lèvres tout en apportant une touche sucrée-salée. Fabriqué à Quimper par un artisan local.',
			'code_barre' => '4897523014133',
			'quantite' => '60',
			'prix' => '4.9',
			'seuil_alerte' => '12',
			'poids' => '15',
			'volume' => '12',
			'categorie' => 'Soin & Hygiène',
			'num_image' => '23',
	
		],
		[
			'email_vendeur' => 'ytanneau@gmail.com',
			'nom_stock' => 'Eau micellaire',
			'nom_public' => 'Eau micellaire à la pomme',
			'description' => 'Nettoyant visage doux et frais',
			'description_detaillee' => 'Eau micellaire formulée avec des extraits de pomme de Bretagne, adaptée à toutes les peaux. Nettoie, démaquille et rafraîchit sans irriter. Testée dermatologiquement.',
			'code_barre' => '4897523014140',
			'quantite' => '25',
			'prix' => '11.5',
			'seuil_alerte' => '5',
			'poids' => '250',
			'volume' => '220',
			'categorie' => 'Soin & Hygiène',
			'num_image' => '24',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-001',
			'nom_public' => 'Marinière classique bretonne',
			'description' => 'Marinière rayée en coton épais, fabrication bretonne traditionnelle.',
			'description_detaillee' => 'Marinière emblématique de la Bretagne, confectionnée en coton épais pour une excellente durabilité. Sa coupe droite et son confort en font un vêtement intemporel, adapté à un usage quotidien.',
			'code_barre' => '3601234567894',
			'quantite' => '80',
			'prix' => '49.0',
			'seuil_alerte' => '15',
			'poids' => '0.32',
			'volume' => '0.0018',
			'categorie' => 'Vêtements',
			'num_image' => '25',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-002',
			'nom_public' => 'Pull marin en laine',
			'description' => 'Pull marin breton en laine naturelle, chaud et résistant.',
			'description_detaillee' => 'Ce pull marin est tricoté en Bretagne à partir de laine naturelle sélectionnée pour sa chaleur et sa robustesse. Idéal pour affronter les embruns et les températures fraîches.',
			'code_barre' => '3601234567895',
			'quantite' => '40',
			'prix' => '89.0',
			'seuil_alerte' => '10',
			'poids' => '0.65',
			'volume' => '0.003',
			'categorie' => 'Vêtements',
			'num_image' => '26',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-003',
			'nom_public' => 'Sweat à capuche Breizh',
			'description' => 'Sweat à capuche confortable avec motif Breizh brodé.',
			'description_detaillee' => 'Sweat à capuche fabriqué en Bretagne, doté d’un tissu doux et résistant. Le motif Breizh brodé apporte une touche identitaire forte tout en restant sobre et moderne.',
			'code_barre' => '3601234567896',
			'quantite' => '60',
			'prix' => '65.0',
			'seuil_alerte' => '10',
			'poids' => '0.5',
			'volume' => '0.0025',
			'categorie' => 'Vêtements',
			'num_image' => '27',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-004',
			'nom_public' => 'T-shirt coton bio Triskell',
			'description' => 'T-shirt en coton biologique avec symbole triskell.',
			'description_detaillee' => 'T-shirt fabriqué à partir de coton biologique certifié. Le triskell, symbole fort de la culture bretonne, est imprimé avec des encres durables pour une longue tenue.',
			'code_barre' => '3601234567897',
			'quantite' => '120',
			'prix' => '29.9',
			'seuil_alerte' => '20',
			'poids' => '0.18',
			'volume' => '0.0012',
			'categorie' => 'Vêtements',
			'num_image' => '28',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-005',
			'nom_public' => 'Veste cirée bretonne',
			'description' => 'Veste cirée imperméable inspirée du vestiaire marin breton.',
			'description_detaillee' => 'Cette veste cirée est conçue pour résister aux intempéries. Fabriquée en Bretagne, elle combine protection contre la pluie, coupe fonctionnelle et style marin authentique.',
			'code_barre' => '3601234567898',
			'quantite' => '35',
			'prix' => '110.0',
			'seuil_alerte' => '5',
			'poids' => '0.9',
			'volume' => '0.004',
			'categorie' => 'Vêtements',
			'num_image' => '29',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-006',
			'nom_public' => 'Bonnet en laine Breizh',
			'description' => 'Bonnet en laine tricoté en Bretagne, chaud et confortable.',
			'description_detaillee' => 'Bonnet confectionné artisanalement en Bretagne à partir de laine de qualité. Sa maille serrée assure une bonne protection contre le froid tout en restant respirante.',
			'code_barre' => '3601234567899',
			'quantite' => '90',
			'prix' => '22.0',
			'seuil_alerte' => '15',
			'poids' => '0.12',
			'volume' => '0.0006',
			'categorie' => 'Vêtements',
			'num_image' => '30',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-007',
			'nom_public' => 'Chemise en lin breton',
			'description' => 'Chemise légère en lin cultivé et tissé en Bretagne.',
			'description_detaillee' => 'Cette chemise est réalisée en lin breton, reconnu pour sa légèreté et sa résistance. Elle offre un confort optimal en été et un style naturel et élégant.',
			'code_barre' => '3601234567900',
			'quantite' => '50',
			'prix' => '75.0',
			'seuil_alerte' => '10',
			'poids' => '0.28',
			'volume' => '0.0016',
			'categorie' => 'Vêtements',
			'num_image' => '31',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-008',
			'nom_public' => 'Écharpe marine rayée',
			'description' => 'Écharpe rayée en laine, inspiration marine bretonne.',
			'description_detaillee' => 'Écharpe douce et chaude, fabriquée en Bretagne. Ses rayures marines rappellent l’univers côtier breton et complètent parfaitement une tenue hivernale.',
			'code_barre' => '3601234567901',
			'quantite' => '70',
			'prix' => '34.0',
			'seuil_alerte' => '10',
			'poids' => '0.25',
			'volume' => '0.0014',
			'categorie' => 'Vêtements',
			'num_image' => '32',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-009',
			'nom_public' => 'Gilet sans manches marin',
			'description' => 'Gilet marin sans manches en laine épaisse.',
			'description_detaillee' => 'Gilet sans manches tricoté en Bretagne, conçu pour offrir chaleur et liberté de mouvement. Inspiré des vêtements traditionnels des marins bretons.',
			'code_barre' => '3601234567902',
			'quantite' => '30',
			'prix' => '79.0',
			'seuil_alerte' => '5',
			'poids' => '0.52',
			'volume' => '0.0028',
			'categorie' => 'Vêtements',
			'num_image' => '33',
	
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			'nom_stock' => 'vetement-010',
			'nom_public' => 'Polo brodé hermine',
			'description' => 'Polo en coton avec broderie hermine bretonne.',
			'description_detaillee' => 'Polo élégant fabriqué en Bretagne, avec une broderie d’hermine discrète sur la poitrine. Un vêtement confortable qui met en valeur l’identité bretonne.',
			'code_barre' => '3601234567903',
			'quantite' => '65',
			'prix' => '45.0',
			'seuil_alerte' => '10',
			'poids' => '0.26',
			'volume' => '0.0015',
			'categorie' => 'Vêtements',
			'num_image' => '34',
	
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Cidre Cornouaille',
			'nom_public' => 'Cidre brut de Cornouaille',
			'description' => 'Cidre AOP au goût fruité et acidulé',
			'description_detaillee' => 'Ce cidre artisanal est élaboré à partir de pommes locales sélectionnées, offrant un équilibre subtil entre amertume et douceur. Idéal pour les repas bretons.',
			'code_barre' => '3256987451227',
			'quantite' => '15',
			'prix' => '6.80',
			'seuil_alerte' => '3', 
			'poids' => '1250', 
			'volume' => '1000', 
			'categorie' => 'Boisson',
			'num_image' => '35',
			'+18' => true
		],
		[
			'email_vendeur' => 'dede@gmail.com',
			'nom_stock' => 'Bière Cornouaille',
			'nom_public' => 'Bière brute de Cornouaille',
			'description' => 'Bière AOP au goût fruité et acidulé',
			'description_detaillee' => 'Cette Bière artisanale est élaborée à partir de pommes locales sélectionnées, offrant un équilibre subtil entre amertume et douceur. Idéal pour les repas bretons.',
			'code_barre' => '3256987451228',
			'quantite' => '15',
			'prix' => '6.80',
			'seuil_alerte' => '3', 
			'poids' => '1250', 
			'volume' => '1000', 
			'categorie' => 'Boisson',
			'num_image' => '36',
			'+18' => true
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Breizh Quest VR',
			'nom_public' => 'Breizh Quest VR',
			'description' => 'Breizh Quest VR est un jeu d’aventure en réalité virtuelle explorant des légendes bretonnes numériques.',
			'description_detaillee' => 'Un jeu VR immersif inspiré des mythes et paysages de Bretagne : plonge dans des quêtes autour de korrigans, arbres sacrés et menhirs mystiques. Conçu pour lunettes VR modernes avec interactions gestuelles, combats tactiles et énigmes environnementales. Chaque niveau te transporte dans une zone emblématique (forêt légendaire, bord de mer houleux, village ancien) avec musique bretonne remixée. Le scénario combine folklore, stratégie et découverte culturelle tout en offrant des défis variés via 12 chapitres progressifs. L’interface inclut des voix en breton et français, tutoriels interactifs, et modes solo ou coop local.',
			'code_barre' => '0445678901234',
			'quantite' => '35',
			'prix' => '49.90',
			'seuil_alerte' => '10', 
			'poids' => '15.0', 
			'volume' => '8', 
			'categorie' => 'Jeux',
			'num_image' => '37'
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Ar C’hoari Breizh',
			'nom_public' => 'Ar C’hoari Breizh',
			'description' => 'Ar C’hoari Breizh est une console portable dédiée aux jeux bretons classiques et modernes.',
			'description_detaillee' => 'Console de jeu portable ergonomique intégrant un écran HD 5″, batterie longue durée et collection exclusive de jeux électroniques inspirés de thèmes bretons : « La Pêche aux Korrigans », « Les Menhirs du Temps », « Fête Interdite à Brocéliande ». Chaque titre combine musique traditionnelle remixée, graphismes néo-rétro et gameplay accessible. Livrée avec 8 jeux préinstallés, ports USB-C pour mises à jour et partage, et fonctions multi-joueurs locales. La console met en valeur la culture bretonne avec illustrations originales créées par artistes locaux. Idéal pour tous âges et pour apprendre quelques mots de breton via mini-jeux éducatifs-ludiques.',
			'code_barre' => '0445678902341',
			'quantite' => '35',
			'prix' => '79.90',
			'seuil_alerte' => '10', 
			'poids' => '300', 
			'volume' => '12', 
			'categorie' => 'Jeux',
			'num_image' => '38'
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			'nom_stock' => 'Breton Beats',
			'nom_public' => 'Breton Beats',
			'description' => 'manette/contrôleur audio-rythme pour jeux électroniques bretons.',
			'description_detaillee' => 'Gamepad unique combinant capteurs de mouvement, pads rétro-éclairés et contrôleur audio pour rythmer tes parties sur consoles et PC. Conçu pour les jeux électroniques intégrant musique bretonne (bagad, bombarde, binioù) où chaque bouton déclenche un sample ou beat différent. Compatible avec jeux de rythme personnalisés pré-installés ou téléchargeables. L’ergonomie bretonne chic intègre motifs celtiques et finitions métal brossé. Fournit un logiciel compagnon pour créer et partager des niveaux musicaux inspirés de fest-noz. Alimentation USB, retour vibratoire adaptatif et profils personnalisables.',
			'code_barre' => '0445678903457',
			'quantite' => '35',
			'prix' => '59.9',
			'seuil_alerte' => '10', 
			'poids' => '220', 
			'volume' => '9', 
			'categorie' => 'Jeux',
			'num_image' => '39'
		],
		[
			'email_vendeur' => 'th@thusaccin.com',
			"nom_stock" => "Menhir Tactique Online",
			"nom_public" => "Menhir Tactique Online",
			"description" => "Jeu de stratégie électronique breton inspiré des menhirs et des clans anciens, mêlant gestion territoriale, combats tactiques et mythologie celtique dans un univers numérique immersif.",
			"description_detaillee" => "Menhir Tactique Online est un jeu électronique de stratégie en temps réel ancré dans l’imaginaire breton. Le joueur incarne un chef de clan chargé de défendre, déplacer et activer des menhirs aux pouvoirs ancestraux. Chaque menhir génère des ressources spécifiques (énergie druidique, savoir ancien, influence spirituelle) permettant de développer des technologies, invoquer des créatures mythologiques ou renforcer ses armées.\n\nLe jeu propose une carte dynamique inspirée de la Bretagne légendaire, avec landes, côtes escarpées et forêts sacrées. Les parties peuvent se jouer en solo contre une intelligence artificielle évolutive ou en ligne jusqu’à 8 joueurs. Les mécaniques stratégiques reposent sur le placement, le timing et la compréhension des synergies entre menhirs.\n\nMenhir Tactique Online intègre une bande-son électronique mêlant instruments traditionnels bretons et ambiances futuristes. Le jeu valorise la culture bretonne tout en offrant une profondeur stratégique accessible aux débutants comme aux joueurs confirmés.",
			"code_barre" => "0445678904564",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 39.90,
			"poids" => 120,
			"volume" => 7,
			'categorie' => 'Jeux',
			'num_image' => '40'
		],

		[
			'email_vendeur' => 'th@thusaccin.com',
			"nom_stock" => "Korrigan Code Academy",
			"nom_public" => "Korrigan Code Academy",
			"description" => "Jeu éducatif électronique breton mêlant programmation, énigmes et folklore, destiné à initier enfants et adultes au code à travers des aventures ludiques.",
			"description_detaillee" => "Korrigan Code Academy est un jeu électronique éducatif conçu pour apprendre les bases de la programmation tout en découvrant l’univers magique de la Bretagne. Le joueur aide une académie de korrigans à réparer des mondes numériques déséquilibrés en utilisant des blocs de code visuels, puis des lignes de code simplifiées.\n\nChaque niveau introduit progressivement des concepts clés comme les conditions, boucles, variables et fonctions, intégrés dans des énigmes scénarisées. Les korrigans, personnages emblématiques du jeu, réagissent en temps réel aux instructions du joueur, rendant l’apprentissage intuitif et amusant.\n\nLe jeu propose plusieurs modes de difficulté, un suivi de progression pédagogique et un lexique bilingue français-breton. L’interface colorée et la musique électronique inspirée de thèmes celtiques renforcent l’immersion. Korrigan Code Academy est idéal pour un usage familial ou scolaire, conciliant apprentissage, culture et divertissement numérique.",
			"code_barre" => "0445678905670",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 29.90,
			"poids" => 100,
			"volume" => 6,
			'categorie' => 'Jeux',
			'num_image' => '41'
		],
		

		[
			'email_vendeur' => 'th@thusaccin.com',
			"nom_stock" => "Brocéliande AR Explorer",
			"nom_public" => "Brocéliande AR Explorer",
			"description" => "Jeu électronique en réalité augmentée permettant d’explorer une forêt bretonne mythique à travers des quêtes numériques interactives et géolocalisées.",
			"description_detaillee" => "Brocéliande AR Explorer est un jeu électronique en réalité augmentée qui transforme l’environnement réel du joueur en une forêt mythologique bretonne. Grâce à la caméra d’un smartphone ou d’une tablette, le joueur découvre des créatures légendaires, artefacts cachés et portails druidiques superposés au monde réel.\n\nLe gameplay repose sur l’exploration, la résolution d’énigmes et la collecte d’objets virtuels liés aux légendes bretonnes. Chaque quête raconte une histoire inspirée de récits anciens, tout en utilisant des mécaniques modernes de jeu électronique.\n\nLe jeu intègre un système de progression, des événements temporaires et des défis collaboratifs. Les graphismes stylisés, combinés à une bande-son électronique atmosphérique, créent une expérience immersive unique. Brocéliande AR Explorer valorise la marche, la curiosité et la découverte culturelle à travers une approche ludique et technologique.",
			"code_barre" => "0445678906787",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 24.90,
			"poids" => 80,
			"volume" => 5,
			'categorie' => 'Jeux',
			'num_image' => '42'
		],
	
		[
			'email_vendeur' => 'th@thusaccin.com',
			"nom_stock" => "Fest-Noz Rhythm Battle",
			"nom_public" => "Fest-Noz Rhythm Battle",
			"description" => "Jeu de rythme électronique breton où le joueur affronte d’autres danseurs virtuels sur des musiques traditionnelles remixées en versions électroniques.",
			"description_detaillee" => "Fest-Noz Rhythm Battle est un jeu électronique de rythme mettant à l’honneur la musique et la danse bretonnes dans une version modernisée. Le joueur doit synchroniser ses actions avec des rythmes inspirés du fest-noz, remixés avec des sons électroniques contemporains.\n\nChaque piste musicale correspond à une danse traditionnelle et propose différents niveaux de difficulté. Le système de score repose sur la précision, la fluidité et la capacité à enchaîner des combos. Le jeu propose un mode solo narratif ainsi qu’un mode multijoueur compétitif local ou en ligne.\n\nLes visuels sont dynamiques et colorés, inspirés des costumes bretons et des motifs celtiques. Une galerie interactive permet de découvrir l’origine des musiques et des danses. Fest-Noz Rhythm Battle est à la fois un jeu énergique et un hommage numérique à la culture bretonne.",
			"code_barre" => "0445678907893",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 34.90,
			"poids" => 110,
			"volume" => 6,
			'categorie' => 'Jeux',
			'num_image' => '43'
		],
	
		[
			'email_vendeur' => 'th@thusaccin.com',
			"nom_stock" => "Armor Defense Grid",
			"nom_public" => "Armor Defense Grid",
			"description" => "Jeu électronique de défense stratégique situé sur les côtes bretonnes, combinant technologie futuriste et légendes maritimes celtiques.",
			"description_detaillee" => "Armor Defense Grid est un jeu électronique de type tower defense se déroulant sur un littoral breton futuriste. Le joueur doit protéger des cités côtières contre des vagues d’ennemis inspirés de créatures marines légendaires et de menaces technologiques.\n\nLes défenses disponibles combinent technologie avancée et pouvoirs mystiques bretons : balises druidiques, canons à énergie marine, tours sonores basées sur la musique traditionnelle. Chaque carte propose des contraintes géographiques spécifiques, comme marées changeantes ou tempêtes numériques.\n\nLe jeu offre une progression stratégique riche, avec arbres de compétences, amélioration des structures et défis hebdomadaires. L’ambiance sonore mêle bruit des vagues, chants marins et musique électronique. Armor Defense Grid propose une expérience tactique profonde, rendant hommage à l’identité maritime bretonne.",
			"code_barre" => "0445678908906",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 44.90,
			"poids" => 130,
			"volume" => 7,
			'categorie' => 'Jeux',
			'num_image' => '44'
		],
		[
			'email_vendeur' => 'ivillalard@gmail.com',
			"nom_stock" => "Menhir LED Connecté",
			"nom_public" => "Menhir LED Connecté",
			"description" => "Décoration lumineuse bretonne en forme de menhir électronique, intégrant des LED programmables pour créer des ambiances inspirées des légendes celtiques.",
			"description_detaillee" => "Le Menhir LED Connecté est une décoration électronique inspirée des pierres levées emblématiques de la Bretagne, revisitées dans un design moderne et technologique. Fabriqué en résine texturée imitation granit, il intègre un système de LED RGB pilotables via application mobile ou télécommande.\n\nLes effets lumineux peuvent simuler une énergie druidique pulsante, des runes lumineuses ou des cycles de couleurs rappelant les landes bretonnes au lever et au coucher du soleil. Plusieurs modes sont disponibles : ambiance douce, éclairage réactif à la musique ou animations programmées.\n\nPensé pour une utilisation intérieure, ce menhir décoratif peut être placé dans un espace gaming, un salon ou une vitrine culturelle. Il combine tradition bretonne et esthétique électronique contemporaine, offrant un objet décoratif à la fois symbolique et immersif.",
			"code_barre" => "0556789012345",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 69.90,
			"poids" => 1800,
			"volume" => 2,
			'categorie' => 'Décoration',
			'num_image' => '45'
		],

		[
			'email_vendeur' => 'ivillalard@gmail.com',
			"nom_stock" => "Panneau Néon Triskell",
			"nom_public" => "Panneau Néon Triskell",
			"description" => "Décoration murale électronique représentant un triskell breton en néon LED, idéale pour créer une ambiance moderne et culturelle dans un espace intérieur.",
			"description_detaillee" => "Le Panneau Néon Triskell est une décoration murale lumineuse mêlant symbolique bretonne et design électronique. Le triskell, symbole de mouvement et d’équilibre, est ici revisité sous forme de néon LED basse consommation aux lignes nettes et contemporaines.\n\nConçu pour être fixé au mur ou posé sur support, ce panneau diffuse une lumière homogène sans éblouissement. Plusieurs teintes sont disponibles ou combinables : blanc chaud, bleu océan, vert lande ou violet électrique. Un variateur d’intensité permet d’adapter l’éclairage à l’ambiance souhaitée.\n\nParfait pour un bureau, une salle de jeu, un commerce ou un événement culturel, ce panneau décoratif affirme une identité bretonne forte tout en s’intégrant à des univers modernes. Il constitue un élément visuel marquant, entre tradition et culture numérique.",
			"code_barre" => "0556789013452",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 54.90,
			"poids" => 900,
			"volume" => 6,
			'categorie' => 'Décoration',
			'num_image' => '46'
		],

		[
			'email_vendeur' => 'ivillalard@gmail.com',
			"nom_stock" => "Totem Korrigan Holographique",
			"nom_public" => "Totem Korrigan Holographique",
			"description" => "Objet décoratif électronique projetant un korrigan holographique animé, inspiré du folklore breton et des technologies futuristes.",
			"description_detaillee" => "Le Totem Korrigan Holographique est une décoration électronique originale combinant folklore breton et projection visuelle avancée. À l’intérieur d’un socle discret se trouve un système de projection holographique donnant vie à un korrigan animé en trois dimensions.\n\nLe korrigan interagit visuellement avec son environnement : il se déplace, observe, disparaît et réapparaît selon des cycles programmés. Plusieurs animations sont disponibles, allant de scènes ludiques à des postures plus mystérieuses, accompagnées de légers effets sonores désactivables.\n\nPensé comme une pièce décorative immersive, ce totem est idéal pour un espace gaming, une vitrine ou un lieu culturel. Il crée un point d’attention fort, mêlant imaginaire breton, innovation électronique et esthétique futuriste, sans nécessiter de lunettes ou d’équipement spécifique.",
			"code_barre" => "0556789014568",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 89.90,
			"poids" => 1200,
			"volume" => 9,
			'categorie' => 'Décoration',
			'num_image' => '47'
		],

		[
			'email_vendeur' => 'ivillalard@gmail.com',
			"nom_stock" => "Carte Murale Interactive de Bretagne",
			"nom_public" => "Carte Murale Interactive de Bretagne",
			"description" => "Carte décorative électronique de la Bretagne intégrant des zones lumineuses interactives liées aux légendes, villes et sites emblématiques.",
			"description_detaillee" => "La Carte Murale Interactive de Bretagne est une décoration électronique éducative et esthétique. Réalisée sur un panneau rigide illustré, elle intègre des capteurs tactiles et des points lumineux correspondant aux régions, villes et lieux mythiques bretons.\n\nEn touchant une zone, des animations lumineuses s’activent et peuvent être accompagnées de sons ou de courtes narrations via un module audio intégré. Les contenus abordent aussi bien la géographie que les légendes, la musique ou l’histoire bretonne.\n\nCette carte décorative s’adresse aussi bien aux particuliers qu’aux espaces culturels ou éducatifs. Elle apporte une touche technologique discrète tout en valorisant l’identité bretonne. Son design sobre permet une intégration harmonieuse dans différents styles d’intérieurs.",
			"code_barre" => "0556789015675",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 74.90,
			"poids" => 1500,
			"volume" => 10,
			'categorie' => 'Décoration',
			'num_image' => '48'
		],

		[
			'email_vendeur' => 'ivillalard@gmail.com',
			"nom_stock" => "Lampe Armorique Ambiante",
			"nom_public" => "Lampe Armorique Ambiante",
			"description" => "Lampe décorative électronique inspirée des côtes bretonnes, diffusant des ambiances lumineuses évoquant la mer, le vent et les légendes maritimes.",
			"description_detaillee" => "La Lampe Armorique Ambiante est une décoration lumineuse électronique conçue pour évoquer l’atmosphère des côtes bretonnes. Son diffuseur texturé rappelle les mouvements de la mer et projette une lumière douce aux variations progressives.\n\nGrâce à un module électronique intégré, la lampe propose plusieurs scénarios lumineux : mer calme, tempête lointaine, crépuscule côtier ou nuit mystique. Les transitions sont fluides et pensées pour favoriser la détente ou l’immersion.\n\nIdéale pour un salon, une chambre ou un espace de jeu, cette lampe décorative associe esthétique naturelle et technologie moderne. Elle incarne une vision contemporaine de la décoration bretonne, alliant sobriété, identité régionale et innovation électronique.",
			"code_barre" => "0556789016782",
			'quantite' => '35',
			'seuil_alerte' => '10', 
			"prix" => 49.90,
			"poids" => 1000,
			"volume" => 8,
			'categorie' => 'Décoration',
			'num_image' => '49'
		]
	];

	foreach($produits as $p) {
		$id_compte = sql_email_compte($pdo, $p['email_vendeur'], 'vendeur')['id_compte'];

		add_produit($id_compte, $p['nom_stock'], $p['nom_public'], $p['prix'], $p['tva'] ?? 20, $p['code_barre'], $p['+18'] ?? false, $p['quantite_achetee'] ?? 1, $p['quantite'], $p['seuil_alerte'], $p['description'], $p['description_detaillee'], $p['poids'], $p['volume'], $p['categorie']);

	
		$id_produit = $pdo->lastInsertId();
		$id_image = add_image("ressources/produit/{$id_produit}_1.png", $p['nom_public'], $p['nom_public']);
		add_image_produit($id_produit, $id_image);

		// Copie l'image du produit (qu'est dans le dossier ressources du peuplement) dans le vrai dossier ressources du site
		$i1 = fopen(__DIR__ . "/ressources_temp/produit/{$p['num_image']}.png", 'r');
		$i2 = fopen(__DIR__ . "/../html/ressources/produit/{$id_produit}_1.png", 'c');

		if (($i1 ?? false) !== false && ($i2 ?? false) !== false) {
			while (fwrite($i2, fread($i1, 1000)));
		} else {
			echo "Erreur de chargement de l'image du produit " . $p['nom_public'] . "\n";
		}
	}
}


function i_avis() {
	global $pdo;

	$avis = [
		[
                'email_client' => 'dudu@gmail.com',
                'nom_produit' => 'Galettes de blé noir',
                'note' => 1,
                'titre' => 'bof',
                'description' => '',
        ],

        [
                'email_client' => 'dudu@gmail.com',
                'nom_produit' => 'Far breton aux pruneaux',
                'note' => 3,
                'titre' => 'Pas mal',
                'description' => 'Bon ok ça cest pas mal en vrai si on y réfléchit bien, mais si on prend mon honnête honnêteté qui est des plus honnête, je trouve quen réalité, ça aurait sûrrement pu être un peu mieux si on y réfléchit bien, parce quen fait cest pas si parfait si on y réfléchit bien tu vois, en fait on pourrait probablement améliorer un peu la recette pour rendre le produit un peu mieux tu vois, mais ce nest que mon honnête avis honnête et subjectif tu vois, menfin bon bref.',
        ],

        [
                'email_client' => 'dudu@gmail.com',
                'nom_produit' => 'Beurre demi-sel de baratte',
                'note' => 5,
                'titre' => 'J\'adore, j\'adhère',
                'description' => '',
        ],

        [
                'email_client' => 'dudu@gmail.com',
                'nom_produit' => 'Caramel au beurre salé',
                'note' => 2,
                'titre' => 'Mouais j\'ai vu mieux je trouve',
                'description' => '',
        ],

        [
                'email_client' => 'dudu@gmail.com',
                'nom_produit' => 'Palets bretons pur beurre',
                'note' => 3,
                'titre' => 'Je suis déçu...',
                'description' => 'Sérieux il arrive quoi à mon Dédé préféré ? je croyais que tavais des talents de vente...',
        ],

        [
                'email_client' => 'edlelay@gmail.com',
                'nom_produit' => 'Galettes de blé noir',
                'note' => 5,
                'titre' => 'Je suis là',
                'description' => 'Cest moi, cest le vrai Eouar, et j\'adore ce produit, cest mon produit préféré ! j\'en mange tous les matins.',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Galettes de blé noir',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Far breton aux pruneaux',
                'note' => 3,
                'titre' => 'Ok',
                'description' => 'Pas mal, j\'en reprendrais si j\'en ai besoin je pense',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Beurre demi-sel de baratte',
                'note' => 1,
                'titre' => 'Nul',
                'description' => 'Très mauvais, je souhaite me faire rembourser',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Caramel au beurre salé',
                'note' => 3,
                'titre' => 'Ok',
                'description' => 'Pas mal, j\'en reprendrais si j\'en ai besoin je pense',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Palets bretons pur beurre',
                'note' => 1,
                'titre' => 'Nul',
                'description' => 'Très mauvais, je souhaite me faire rembourser',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Soupe de poissons bretonne',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Rillettes de maquereau au cidre',
                'note' => 4,
                'titre' => 'Super',
                'description' => 'Super produit, j\'ai adoré, mais il est arrivé avec une petit tache',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Crêpes dentelle de Bretagne',
                'note' => 1,
                'titre' => 'Nul',
                'description' => 'Très mauvais, je souhaite me faire rembourser',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Smartphone ArmorX5',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Casque audio BreizhSound Pro',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Montre connectée CeltWatch S',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Ordinateur portable ArmorBook 15',
                'note' => 4,
                'titre' => 'Super',
                'description' => 'Super produit, j\'ai adoré, mais il est arrivé avec une petit tache',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Enceinte Bluetooth BreizhBass',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Appareil photo ArmorCam ZR100',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Tablette BreizhTab 10',
                'note' => 4,
                'titre' => 'Super',
                'description' => 'Super produit, j\'ai adoré, mais il est arrivé avec une petit tache',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Clavier mécanique ArmorKey RGB',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Souris sans fil BreizhMouse X',
                'note' => 1,
                'titre' => 'Nul',
                'description' => 'Très mauvais, je souhaite me faire rembourser',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Écouteurs ArmorPods Air+',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Savon au lait de brebis',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Crème mains au miel de sarrasin',
                'note' => 3,
                'titre' => 'Ok',
                'description' => 'Pas mal, j\'en reprendrais si j\'en ai besoin je pense',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Shampoing solide aux algues',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Baume à lèvres au beurre salé',
                'note' => 4,
                'titre' => 'Super',
                'description' => 'Super produit, j\'ai adoré, mais il est arrivé avec une petit tache',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Eau micellaire à la pomme',
                'note' => 4,
                'titre' => 'Super',
                'description' => 'Super produit, j\'ai adoré, mais il est arrivé avec une petit tache',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Marinière classique bretonne',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Pull marin en laine',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Sweat à capuche Breizh',
                'note' => 1,
                'titre' => 'Nul',
                'description' => 'Très mauvais, je souhaite me faire rembourser',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'T-shirt coton bio Triskell',
                'note' => 3,
                'titre' => 'Ok',
                'description' => 'Pas mal, j\'en reprendrais si j\'en ai besoin je pense',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Veste cirée bretonne',
                'note' => 3,
                'titre' => 'Ok',
                'description' => 'Pas mal, j\'en reprendrais si j\'en ai besoin je pense',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Bonnet en laine Breizh',
                'note' => 2,
                'titre' => 'Bof',
                'description' => 'Moyen, en vrai je pense que ça pourrait être mieux',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Chemise en lin breton',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Écharpe marine rayée',
                'note' => 1,
                'titre' => 'Nul',
                'description' => 'Très mauvais, je souhaite me faire rembourser',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Gilet sans manches marin',
                'note' => 5,
                'titre' => 'Incroyable',
                'description' => 'Super produit, j\'adore, j\'en reprendrais',
        ],

        [
                'email_client' => 'commentateur@gmail.com',
                'nom_produit' => 'Polo brodé hermine',
                'note' => 3,
                'titre' => 'Ok',
                'description' => 'Pas mal, j\'en reprendrais si j\'en ai besoin je pense',
        ],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Galettes de blé noir',
				'note' => '2',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Far breton aux pruneaux',
				'note' => '3',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Beurre demi-sel de baratte',
				'note' => '4',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Caramel au beurre salé',
				'note' => '3',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Palets bretons pur beurre',
				'note' => '1',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Soupe de poissons bretonne',
				'note' => '2',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Rillettes de maquereau au cidre',
				'note' => '2',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Crêpes dentelle de Bretagne',
				'note' => '3',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Smartphone ArmorX5',
				'note' => '4',
		],

		[
				'email_client' => 'didi@gmail.com',
				'nom_produit' => 'Pull marin en laine',
				'note' => '2',
		],

		[
				'email_client' => 'dudu@gmail.com',
				'nom_produit' => 'Crêpes dentelle de Bretagne',
				'note' => '2',
		],

		[
				'email_client' => 'edlelay@gmail.com',
				'nom_produit' => 'Crêpes dentelle de Bretagne',
				'note' => '3',
		]
	];

	
	foreach($avis as $a) {
		$id_produit= $pdo->query("SELECT id_produit FROM _produit WHERE nom_public = '{$a['nom_produit']}'")->fetch(PDO::FETCH_ASSOC)['id_produit'];
		$id_compte = sql_email_compte($pdo, $a['email_client'], 'client')['id_compte'];
		cree_avis($id_compte, $id_produit, $a['note'], $a['titre'] ?? null, $a['description'] ?? null, null);
	}
}


function i_promo() {
	global $pdo;

	$promotions = [
		[
                'nom_produit' => 'Galettes de blé noir',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '10',
        ],

        [
                'nom_produit' => 'Palets bretons pur beurre',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '5',
        ],

        [
                'nom_produit' => 'Soupe de poissons bretonne',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '5',
        ],

        [
                'nom_produit' => 'Rillettes de maquereau au cidre',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
        ],

        [
                'nom_produit' => 'Smartphone ArmorX5',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '25',
        ],

        [
                'nom_produit' => 'Tablette BreizhTab 10',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '25',
        ],

        [
                'nom_produit' => 'Écouteurs ArmorPods Air+',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
        ],

        [
                'nom_produit' => 'Marinière classique bretonne',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
        ],

        [
                'nom_produit' => 'Pull marin en laine',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '5',
        ],

        [
                'nom_produit' => 'Gilet sans manches marin',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '20',
        ],

        [
                'nom_produit' => 'Polo brodé hermine',
                'date_debut' => '2026-01-20',
                'date_fin' => '2026-01-23',
                'reduction' => '15',
        ],

	];
	
	foreach ($promotions as $p) {
		$id_produit= $pdo->query("SELECT id_produit FROM _produit WHERE nom_public = '{$p['nom_produit']}'")->fetch(PDO::FETCH_ASSOC)['id_produit'];

		creer_promotion($id_produit, $p['date_debut'], $p['date_fin'], $p['reduction'] ?? 0, null);
	}

}

vider_tables();

i_question_secu();
i_categories();
i_client();
i_vendeur();
i_produit();
i_avis();
i_promo();