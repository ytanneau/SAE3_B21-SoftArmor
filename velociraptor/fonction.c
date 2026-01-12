#include "constante.h"
#include "fonction.h"



compte* init_compte(const char* chemain){
    compte* res = NULL;
    //ouverture du fichier des compte
    FILE *file = fopen( chemain, "r");
    // si le fichier na pas pu etre ouver on ferme le programe
    if (file == NULL) {
        exit(EXIT_FAILURE);
    }

    char ligne[TAILLE];
    //on lit les ligne une par une
    while (fgets(ligne, sizeof(ligne), file)) 
    { 
        // on recupère id
        char *id = strtok(ligne, DELIMITER);
        // on recupère le mdp
        char *mdp = strtok(NULL, DELIMITER);

        if (mdp[MDP_TAILLE-1] == '\n')
        {
            mdp[MDP_TAILLE-1] = '\0';
        }
        

        // si les champ ne son pas vide et que le mode passe, que l'id est superier a 0 caractère et que le mode passe mesure 32 caractere ou 33 si le dernié est un retout a la ligne
        if(id != NULL && mdp != NULL && strlen(id) > 0 && (strlen(mdp) == MDP_TAILLE-1 || (strlen(mdp) == MDP_TAILLE && mdp[MDP_TAILLE-1] == '\n')))
        {
            // inisalisation du nouveau compte
            compte* nouv = (compte*)malloc(sizeof(compte));
            strcpy(nouv->id,id);
            strcpy(nouv->mdp,mdp);
            nouv->next = NULL;


            if (res == NULL){
                res = nouv;
            }
            else{
                compte* nav = res;
                while (nav->next != NULL)
                {
                    nav = nav->next;
                }
                nav->next = nouv;
            }
        }
    }

    // fermeture du fichier des compte
    fclose(file);
    // si il y a pas des compte son on arrète le programme
    if (res == NULL)
    {
        fprintf(stderr, "[FATAL] pas de compte trouver\n");
        exit(EXIT_FAILURE);
    }
    
    return res;
}

// affiche la liste des compte
void affiche_compte(compte* c)
{
    compte* nav = c;
    if (nav == NULL)
    {
        printf("pas de compte trouver\n");
    }
    else
    {
        printf("ID : %s\n", nav->id);
        printf("MDP : %s\n", nav->mdp);
        nav = nav->next;

        while (nav != NULL)
        {
            printf("ID : %s\n", nav->id);
            printf("MDP : %s\n", nav->mdp);
            nav = nav->next;
        }
    }
}

//-------------------------------------------------------------------------------------------------------


void comminication(int cnx, compte* c, bool colisInfinit, int nbColisMax, MYSQL *conn)
{
    bool connect = false;
    int instruction;
    int readNb;
    char buff[TRAME_TAILLE];

    while (true)
    {
        if (read(cnx, buff, TRAME_TAILLE) == -1)
        {
            printf("[ERROR] READ\n");
            fin(cnx);
        }
         
        instruction = atoi(strtok(buff, INSTRUCTION_DELIMITER));
        if (DEBUG)
        {
            printf("[DEBUG] INSTRUCTION : %d\n", instruction);
        }

        switch (instruction)
        {
            case INSTRUCTION_FIN:
                fin(cnx);
                break;
            

            case INSTRUCTION_CONNECTION:
                if (!connect)
                {
                    connect = authtification( c, buff);
                    connection(cnx, connect);                
                }
                else
                {
                    message_erreur(cnx, ERREUR_INSTRUCTION);
                }
                break;


            case INSTRUCTION_NEW_COLIS:
                if (connect)
                {
                    new_colis(cnx, colisInfinit, nbColisMax, conn);
                }
                else
                {
                    message_erreur(cnx, ERREUR_ACCES);
                    fin(cnx);
                }
                break;


            case INSTRUCTION_INFO_COLIS:
                if (connect)
                {
                    info_colis(cnx, strtok(NULL, INSTRUCTION_DELIMITER), conn);
                }
                else
                {
                    message_erreur(cnx, ERREUR_ACCES);
                    fin(cnx);
                }
                break;

            case INSTRUCTION_PHOTO_COLIS:
                if (connect)
                {
                    photo(cnx, strtok(NULL, INSTRUCTION_DELIMITER), conn);
                }
                else
                {
                    message_erreur(cnx, ERREUR_ACCES);
                    fin(cnx);
                }
                break;

            default:
                message_erreur(cnx, ERREUR_INSTRUCTION);
                fin(cnx);
                break;
        }
    }
}

void fin(int cnx)
{
    shutdown(cnx,SHUT_RDWR);
    close(cnx);
    _exit(EXIT_SUCCESS);
}

void tombe(int sig)
{
    wait(NULL);
}

//-------------------------------------------------------------------------------------------------------

void envoier_message(int cnx, char *message)
{
    if (write(cnx, message, strlen(message)) == -1)
    {
        printf("[ERROR] WRITE\n");
        fin(cnx);
    }
    if (DEBUG)
    {
        printf("[DEBUG] SEND : %s\n",message);
    }
}


void message_erreur(int cnx, int valeur)
{
    char message[TAILLE];
    sprintf(message, "%s%s%d", ERREUR, DELIMITER, valeur); // formate le message
    envoier_message(cnx, message);
    
}

void envoier_code(int cnx, char *message){
    if (write(cnx, message, strlen(message)) == -1)
    {
        printf("[ERROR] WRITE\n");
        fin(cnx);
    }
}

//-------------------------------------------------------------------------------------------------------


bool authtification(compte* c, char buff[TAILLE])
{
    bool connect = false;
    char *id = strtok(NULL, INSTRUCTION_DELIMITER);
    char *mdp = strtok(NULL, "\n");

    if (DEBUG)
    {
        printf("[DEBUG] CONNECT : \n");
        printf("ID : %s\n", id);
        printf("MDP : %s\n", mdp);
    }

    compte* i = c;
    // boucle pour comparé avec chaque compte
    while (i != NULL)
    {
        if (strcmp(id,i->id) == 0 && strncmp(mdp,i->mdp, MDP_TAILLE-1) == 0)
        {
            connect = true;
        }
        i = i->next;
    }
    return connect;
}


void connection(int cnx, bool connect)
{
    char message[TAILLE];
    if (connect)
    {
        sprintf(message,"%s%s1",CONNECTION,DELIMITER);
        if (DEBUG)
        {
            printf("[DEBUG] CONNECT : TRUE\n");
            envoier_message(cnx, message);
        }
    }
    else
    {
        sprintf(message,"%s%s0",CONNECTION,DELIMITER);
        if (DEBUG)
        {
            printf("[DEBUG] CONNECT : FALSE\n");
            envoier_message(cnx, message);
            fin(cnx);
        }
    }
}

//-------------------------------------------------------------------------------------------------------

void new_colis(int cnx, bool colisInfinit, int nbColisMax, MYSQL *conn)
{
    char message[TAILLE];
    if (colisInfinit || nbColisMax > colis_encour(conn, cnx))
    {
        char code[BORDEREAU_SIZE];
        genere_code(code);
        if (BDD)
        {
            while (colis_existe(conn, cnx, code)){
                genere_code(code);
            }
        
            char sql[200];
            sprintf(sql, "INSERT INTO _colis (bodereau) VALUES (%s)", code);

            if (mysql_query(conn, sql))
            { 
                fprintf(stderr, "Erreur requête : %s\n", mysql_error(conn)); 
                mysql_close(conn); 
                fin(cnx); 
            }

            sprintf(message, "%s%s%s", COLIS, DELIMITER, code);
            envoier_message(cnx, message);
        
        }
        else{
            sprintf(message, "%s%s%s", COLIS, DELIMITER, code);
            envoier_message(cnx, message);
        }
    }
    else
    {
        sprintf(message, "%s%s%d", ERREUR, DELIMITER, ERREUR_NEW_COLIS);   
    }
}

void genere_code(char *code) 
{ 
    size_t charset_size = sizeof(BORDEREAU_CARACTERE) - 1; // -1 pour exclure '\0' 
    for (size_t i = 0; i < BORDEREAU_SIZE-1; i++) 
    { 
        int key = rand() % charset_size;
        code[i] = BORDEREAU_CARACTERE[key]; 
    } 
    code[BORDEREAU_SIZE] = '\0'; // fin de chaîne

    if (DEBUG)
    {
        printf("[DEBUG] CODE GENERATE : %s\n",code);
    }
}

int colis_encour(MYSQL *conn, int cnx)
{
    if (BDD)
    {
        if (mysql_query(conn, "SELECT id, nom FROM utilisateurs")) 
        { 
            fprintf(stderr, "Erreur requête : %s\n", mysql_error(conn));
            //mysql_close(conn); 
            fin(cnx); 
        }
        MYSQL_RES *res = mysql_store_result(conn); 
        MYSQL_ROW row;
        row = mysql_fetch_row(res);
        return atoi(row[1]);
    }
    
    return 1;
}

bool colis_existe(MYSQL *conn, int cnx, char *code)
{
    char sql[200];
    sprintf(sql, "SELECT * FROM _colis WHERE bordereau = %s", code);

    if (mysql_query(conn, sql)) 
    { 
        fprintf(stderr, "Erreur requête : %s\n", mysql_error(conn)); 
        mysql_close(conn); 
        fin(cnx);
    }
    MYSQL_RES *res = mysql_store_result(conn); 
    MYSQL_ROW row;

    if (row[0] == NULL){
        return false;
    }
    return true;
}


//-------------------------------------------------------------------------------------------------------


void info_colis(int cnx, char* code, MYSQL *conn)
{

    if (check_code(code))
    {
        if (DEBUG)
        {
            printf("[DEBUG] CODE : TRUE\n");
        }
        
        char message[TAILLE];

        if (BDD)
        {
            if (mysql_query(conn, "SELECT * FROM _colis")) 
            { 
                fprintf(stderr, "Erreur requête : %s\n", mysql_error(conn));
                //mysql_close(conn); 
                fin(cnx); 
            } 
            MYSQL_RES *res = mysql_store_result(conn); 
            MYSQL_ROW row; 
            while ((row = mysql_fetch_row(res))) 
            { 
                printf("ID: %s, Nom: %s\n", row[0], row[1]); 
            } 
            mysql_free_result(res); 
            mysql_close(conn);
        }
        else
        {
            sprintf(message, "%s%s1\n%s%s%s\n%s%s%s", ETAPE, DELIMITER, MODE, DELIMITER, VIDE, CAUSE, DELIMITER, VIDE);
            envoier_message(cnx, message);
        }
    }
    else
    {
        if (DEBUG)
        {
            printf("[DEBUG] CODE : FALSE\n");
        }
        message_erreur(cnx, ERREUR_COLIS_INEXISTENT);
        fin(cnx);
    }
}

bool check_code(char* code)
{
    int x, y;
    bool good;
    code[BORDEREAU_SIZE] = '\0';
    //printf("code : %s\n", code);
    //printf("size : %ld\n", strlen(code));

    for (x = 0; x < BORDEREAU_SIZE-1; x++)
    {
        good = false;
        y = 0;
        //printf("x : %c\n", code[x]);
        while (!good && y<strlen(BORDEREAU_CARACTERE))
        {
            //printf("y : %c\n", BORDEREAU_CARACTERE[y]);
            if (code[x] == BORDEREAU_CARACTERE[y])
            {
                good = true;
                //printf("test %c\n", code[x]);
            }
            y++;
        }

        if (!good)
        {
            //printf("raison : %c\n", code[x]);
            return false;
        }
    }
    return true;
}

//----------------------------------------------------------------------

void photo(int cnx, char* code, MYSQL* conn)
{
    if (check_code(code))
    {
        if (BDD)
        {
            
        }
        else
        {
            envoier_photo(cnx, FICHIER_PHOTO);
        }
    }
    else
    {
        message_erreur(cnx, ERREUR_COLIS_INEXISTENT);
    }
}

void envoier_photo(int cnx, char *fichier)
{
    char buff[TAILLE_PHOTO];
    char message[TAILLE_PHOTO*8];
    //printf("test fonction\n");
    sprintf(message, "%s%s", PHOTO, DELIMITER);
    envoier_message(cnx, message);

    int fd = open(FICHIER_PHOTO, O_RDONLY);
    while (read(fd, buff, TAILLE_PHOTO) != 0)
    {
        //printf("testest\n");
        encode_photo(buff, message);
        envoier_code(cnx, message);
    }
}


void encode_photo(char *src, char *des)
{
    for (int i = 0; src[i] != '\0'; i++) 
    {
        unsigned char c = (unsigned char)src[i];
        // Conversion du caractère en binaire (8 bits) 
        for (int bit = 7; bit >= 0; bit--) 
        { 
            des[i * 8 + (7 - bit)] = (c & (1 << bit)) ? '1' : '0'; 
        } 
    }
    
    // Ajout du terminateur 
    int len = 0; 
    for (; src[len] != '\0'; len++); 
    des[len * 8] = '\0';

    //printf("%s", des);
}