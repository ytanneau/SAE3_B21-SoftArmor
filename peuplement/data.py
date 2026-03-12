from datetime import datetime, timedelta
import random

def random_datetime(min_date: str, max_date: str) -> str:
    """
    Genère une date aleatoire entre min_date et max_date.
    Format attendu pour les paramètres : 'YYYY-MM-DD HH:MM:SS'
    Retourne une chaîne au même format.
    """
    # Conversion des chaînes en objets datetime
    start = datetime.strptime(min_date, "%Y-%m-%d %H:%M:%S")
    end = datetime.strptime(max_date, "%Y-%m-%d %H:%M:%S")

    # Difference totale en secondes
    delta = end - start
    delta_seconds = delta.total_seconds()

    # Tirage aleatoire
    random_seconds = random.uniform(0, delta_seconds)

    # Date finale
    result = start + timedelta(seconds=random_seconds)
    return result.strftime("%Y-%m-%d %H:%M:%S")


id_produit = [69,70,71,72,73,74,75,76,77,78,96,97,98,99,100,101,102,103]
nom_produit = ["Smartphone ArmorX5",
"Casque audio BreizhSound Pro",
"Montre connectee CeltWatch S",
"Ordinateur portable ArmorBook 15",
"Enceinte Bluetooth BreizhBass",
"Appareil photo ArmorCam ZR100",
"Tablette BreizhTab 10",
"Clavier mecanique ArmorKey RGB",
"Souris sans fil BreizhMouse X",
"ecouteurs ArmorPods Air+",
"Breizh Quest VR",
"Ar Choari Breizh",
"Breton Beats",
"Menhir Tactique Online",
"Korrigan Code Academy",
"Broceliande AR Explorer",
"Fest-Noz Rhythm Battle",
"Armor Defense Grid"]
id_vendeur = 30
id_client = [25,26,28,33,35,37,38,41,45,46,55,57,59,73,77,79,27,36,40]

date_min = "2025-03-09 00:00:00"
date_max = "2026-04-01 00:00:00"
id_commande = [i for i in range(130, 229)]


def create_commande():
    res = []
    for i in range(1000):
        res.append(f"('{random_datetime(date_min, date_max)}', {id_client[int(random.uniform(0, len(id_client)-1))]})")
    
    with open("data_commande.sql", "a") as f:
        f.write("INSERT INTO _commande (date_commande, id_client) VALUES ")
        for i in res:
            f.write(i+",")




#--------------------------------------------------
def create_ele_commande():
    res = []
    for i in range(100):
        res.append(f"""({id_commande[int(random.uniform(0, len(id_commande)-1))]},{id_produit[int(random.uniform(0, len(id_produit)-1))]},{int(random.uniform(0, 10))},{int(random.uniform(10, 250))},'{nom_produit[int(random.uniform(0, len(nom_produit)-1))]}','ThusaCorp')""")
    
    with open("data_elt_commande.sql", "a") as f:
        f.write("INSERT INTO _elt_commande (id_commande, id_produit, quantite, prix, nom_produit, nom_vendeur) VALUES ")
        for i in res:
            f.write(i+",")

#create_commande()
create_ele_commande()