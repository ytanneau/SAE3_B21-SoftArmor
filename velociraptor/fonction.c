#include "constante.h"
#include "fonction.h"



// permet de recupérer les COMPTE dans le fichier
COMPTE* init_compte(const char* chemain, FILE *logI){
    COMPTE* res = NULL;
    //ouverture du fichier des COMPTE
    FILE *file = fopen( chemain, "r");
    // si le fichier na pas pu etre ouver on ferme le programe
    if (file == NULL) {
        fprintf(logI, "[FATAL] ACOUNT FILE NOT FIND\n");
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
            // inisalisation du nouveau COMPTE
            COMPTE* nouv = (COMPTE*)malloc(sizeof(COMPTE));
            strcpy(nouv->id,id);
            strcpy(nouv->mdp,mdp);
            nouv->next = NULL;

            // place le COMPTE dans ma liste
            if (res == NULL){
                res = nouv;
            }
            else{
                COMPTE* nav = res;
                while (nav->next != NULL)
                {
                    nav = nav->next;
                }
                nav->next = nouv;
            }
        }
    }

    // fermeture du fichier des COMPTE
    fclose(file);
    // si il y a pas des COMPTE son on arrète le programme
    if (res == NULL)
    {
        fprintf(logI, "[FATAL] NO ACOUNT FIND\n");
        exit(EXIT_FAILURE);
    }
    
    return res;
}

// affiche la liste des COMPTE
void affiche_compte(COMPTE* c, FILE *logI)
{
    COMPTE* nav = c;
    if (nav == NULL)
    {
        fprintf(logI,"pas de COMPTE trouver\n");
    }
    else
    {
        fprintf(logI, "ID : %s\n", nav->id);
        fprintf(logI, "MDP : %s\n", nav->mdp);
        nav = nav->next;

        while (nav != NULL)
        {
            fprintf(logI, "ID : %s\n", nav->id);
            fprintf(logI, "MDP : %s\n", nav->mdp);
            nav = nav->next;
        }
    }
}

void init_bdd(MYSQL *conn, FILE *logI)
{
    if (conn == NULL) 
    { 
        fprintf(logI, "[FATAL] logI MYSQL\n"); 
        exit(EXIT_FAILURE); 
    }

    COMPTE* res = NULL;
    //ouverture du fichier des COMPTE
    FILE *file = fopen(".env", "r");
    // si le fichier na pas pu etre ouver on ferme le programe
    if (file == NULL) {
        fprintf(logI, "[FATAL] .env NOT FIND\n");
        exit(EXIT_FAILURE);
    }
    // obliger déclaré une ligne pour chaque info, car sinon perte dinformation

    char *bdd_host;
    char *bdd_user;
    char *bdd_password;
    char *bdd_name;
    char *bdd_port;
    
    char ligne1[TAILLE];
    fgets(ligne1, sizeof(ligne1), file);
    strtok(ligne1, DELIMITER);
    bdd_host = strtok(NULL, DELIMITER);
    bdd_host[strlen(bdd_host)-1] = '\0';

    char ligne2[TAILLE];
    fgets(ligne2, sizeof(ligne2), file);
    strtok(ligne2, DELIMITER);
    bdd_user = strtok(NULL, DELIMITER);
    bdd_user[strlen(bdd_user)-1] = '\0';

    char ligne3[TAILLE];
    fgets(ligne3, sizeof(ligne3), file);
    strtok(ligne3, DELIMITER);
    bdd_password = strtok(NULL, DELIMITER);
    bdd_password[strlen(bdd_password)-1] = '\0';

    char ligne4[TAILLE];
    fgets(ligne4, sizeof(ligne4), file);
    strtok(ligne4, DELIMITER);
    bdd_name = strtok(NULL, DELIMITER);
    bdd_name[strlen(bdd_name)-1] = '\0';

    char ligne5[TAILLE];
    fgets(ligne5, sizeof(ligne5), file);
    strtok(ligne5, DELIMITER);
    bdd_port = strtok(NULL, DELIMITER);
    if (bdd_port[strlen(bdd_port)-1] == '\n')
    {
        bdd_port[strlen(bdd_port)-1] = '\0';
    }

    fprintf(logI, "%s BDD : \n", SERVER);
    fprintf(logI, "BDD_HOST %s\n", bdd_host);
    fprintf(logI, "BDD_USER %s\n", bdd_user);
    //fprintf(logI, "BDD_PASSWORD %s\n", bdd_password);
    fprintf(logI, "BDD_NAME %s\n", bdd_name);
    fprintf(logI, "BDD_PORT %d\n", atoi(bdd_port));

    if (mysql_real_connect(conn, bdd_host, bdd_user, bdd_password, bdd_name, atoi(bdd_port), NULL, 0) == NULL) { 
        fprintf(logI, "[FATAL] CONNECT MYSQL : %s\n", mysql_error(conn)); 
        exit(EXIT_FAILURE); 
    }
    fprintf(logI, "%s SUCCESS CONNECT MYSQL\n", SERVER);
}

//-------------------------------------------------------------------------------------------------------


void comminication(SESSION *data, COMPTE* c, bool colisInfinit, int nbColisMax)
{
    log_line(data, "connect open");
    bool connect = false;
    int instruction;
    int readNb;
    char buff[TAILLE_GRAND];
    char message[TAILLE];

    struct pollfd pfd; 
    pfd.fd = data->cnx; 
    pfd.events = POLLIN;

    while (true && poll(&pfd, 1, TIME*1000) !=0)
    {
        readNb = read(data->cnx, buff, TAILLE_GRAND);
        if (readNb == -1)
        {
            log_line(data, "[ERROR] READ");
            fin(data);
        }
        buff[readNb] = '\0';

        instruction = atoi(strtok(buff, INSTRUCTION_DELIMITER));
        sprintf(message, "instruction : %d", instruction);
        log_line(data, message);

        if (DEBUG)
        {
            printf("[DEBUG] INSTRUCTION : %d\n", instruction);
        }

        switch (instruction)
        {
            case INSTRUCTION_FIN:
                log_line(data, "connect close");
                fin(data);
                break;
            

            case INSTRUCTION_CONNECTION:
                if (!connect)
                {
                    connect = authtification(data, c);
                    connection(data, connect);                
                }
                else
                {
                    message_erreur(data, ERREUR_INSTRUCTION);
                }
                break;


            case INSTRUCTION_NEW_COLIS:
                if (connect)
                {
                    new_colis(data, colisInfinit, nbColisMax);
                }
                else
                {
                    message_erreur(data, ERREUR_ACCES);
                }
                break;


            case INSTRUCTION_INFO_COLIS:
                if (connect)
                {
                    info_colis(data);
                }
                else
                {
                    message_erreur(data, ERREUR_ACCES);
                }
                break;

            case INSTRUCTION_PHOTO_COLIS:
                if (connect)
                {
                    photo(data);
                }
                else
                {
                    message_erreur(data, ERREUR_ACCES);
                }
                break;

            default:
                message_erreur(data, ERREUR_INSTRUCTION);
                break;
        }
    }
    message_erreur(data, ERREUR_TIME_OUT);
}

void fin(SESSION *data)
{
    shutdown(data->cnx, SHUT_RDWR);
    close(data->cnx);
    _exit(EXIT_SUCCESS);
}

void tombe(int sig)
{
    wait(NULL);
}

//-------------------------------------------------------------------------------------------------------

void envoier_message(SESSION *data, char *message)
{
    if (write(data->cnx, message, strlen(message)) == -1)
    {
        log_line(data, "[ERROR] WRITE");
        fin(data);
    }
    char res[TAILLE_GRAND];
    log_transforme(message);
    sprintf(res, "reponce : %s", message);
    log_line(data, res);
    if (DEBUG)
    {
        printf("[DEBUG] SEND : %s\n",message);
    }
}


void message_erreur(SESSION *data, int valeur)
{
    char message[TAILLE];
    sprintf(message, "%s%s%d", ERREUR, DELIMITER, valeur); // formate le message
    envoier_message(data, message);
    fin(data);
}

void envoier_code(SESSION *data, char *message){
    if (write(data->cnx, message, strlen(message)) == -1)
    {
        log_line(data, "[ERROR] WRITE PHOTO");
        fin(data);
    }
}

//-------------------------------------------------------------------------------------------------------


bool authtification(SESSION *data, COMPTE* c)
{
    bool connect = false;

    char *id = strtok(NULL, INSTRUCTION_DELIMITER);
    if (id == NULL)
    {
        message_erreur(data, ERREUR_INSTRUCTION);
    }
    
    char *mdp = strtok(NULL, DELIMITER);
    if (mdp == NULL)
    {
        message_erreur(data, ERREUR_INSTRUCTION);
    }
    if (mdp[strlen(mdp)-1] == '\n')
    {
        mdp[strlen(mdp)-1] == '\0';
    }
    

    if (DEBUG)
    {
        printf("[DEBUG] CONNECT : \n");
        printf("ID : %s\n", id);
        printf("MDP : %s\n", mdp);
    }

    COMPTE* i = c;
    // boucle pour comparé avec chaque COMPTE
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


void connection(SESSION *data, bool connect)
{
    char message[TAILLE];
    if (connect)
    {
        sprintf(message,"%s%s1",CONNECTION,DELIMITER);
        if (DEBUG)
        {
            printf("[DEBUG] CONNECT : TRUE\n");
        }
        envoier_message(data, message);
    }
    else
    {
        sprintf(message,"%s%s0",CONNECTION,DELIMITER);
        if (DEBUG)
        {
            printf("[DEBUG] CONNECT : FALSE\n");
        }
        envoier_message(data, message);
        fin(data);
    }
}

//-------------------------------------------------------------------------------------------------------

void new_colis(SESSION *data, bool colisInfinit, int nbColisMax)
{
    char message[TAILLE];
    if (colisInfinit || nbColisMax > colis_encour(data))
    {
        char code[BORDEREAU_SIZE];
        genere_code(code);

        if (BDD)
        {
            while (colis_existe(data, code)){
                genere_code(code);
            }

            char sql[TAILLE_SQL];
            sprintf(sql, "INSERT INTO _colis (bordereau) VALUES ('%s')", code);

            if (mysql_query(data->conn, sql))
            { 
                sprintf(message, "Erreur requête : %s\n", mysql_error(data->conn)); 
                log_line(data, message);
                message_erreur(data, ERREUR_INTERNE);
            }

            sprintf(message, "%s%s%s", COLIS, DELIMITER, code);
            envoier_message(data, message);
        
        }
        else{
            sprintf(message, "%s%s%s", COLIS, DELIMITER, code);
            envoier_message(data, message);
        }
    }
    else
    {
        // envoier une erreur nouveau colis
        message_erreur(data, ERREUR_NEW_COLIS);  
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
    code[BORDEREAU_SIZE-1] = '\0'; // fin de chaîne

    if (DEBUG)
    {
        printf("[DEBUG] CODE GENERATE : %s\n",code);
    }
}

int colis_encour(SESSION *data)
{
    char message[TAILLE_GRAND];
    if (BDD)
    {
        char sql[TAILLE_SQL];
        sprintf(sql, "SELECT * FROM %s", VIEW);

        if (mysql_query(data->conn, sql)) 
        { 
            sprintf(message, "Erreur requête : %s\n", mysql_error(data->conn));
            log_line(data, message);
            message_erreur(data, ERREUR_INTERNE);
        }

        MYSQL_RES *res = mysql_store_result(data->conn); 
        MYSQL_ROW row = mysql_fetch_row(res);

        if (DEBUG)
        {
           printf("[DEBUG] COLIS EN COUR : %s\n", row[0]); 
        }
        
        
        return atoi(row[0]);
    }
    
    return 1;
}

bool colis_existe(SESSION *data, char *code)
{
    char message[TAILLE_GRAND];
    char sql[TAILLE_SQL];
    sprintf(sql, "SELECT %s FROM %s WHERE bordereau = '%s'", COLON_ETAPE, TABLE, code);

    if (mysql_query(data->conn, sql)) 
    { 
        sprintf(message, "Erreur requête : %s\n", mysql_error(data->conn));
        log_line(data, message);
        message_erreur(data, ERREUR_INTERNE);
    }

    MYSQL_RES *res = mysql_store_result(data->conn);
    MYSQL_ROW row = mysql_fetch_row(res);

    if (NULL == row){
        if (DEBUG)
        {
            printf("[DEBUG] SQL ROW VIDE\n");
        }
        return false;
    }

    if (DEBUG)
    {
        printf("[DEBUG] SQL ROW NON VIDE\n");
    }
    return true;
}


//-------------------------------------------------------------------------------------------------------


void info_colis(SESSION *data)
{
    char *code = strtok(NULL, INSTRUCTION_DELIMITER);
    if (code == NULL)
    {
        message_erreur(data, ERREUR_INSTRUCTION);
    }
    
    if (check_code(code))
    {
        if (DEBUG)
        {
            printf("[DEBUG] CODE : TRUE\n");
        }
        
        char message[TAILLE*4];
        if (BDD)
        {
            
            if (colis_existe(data, code))
            {
                char sql[TAILLE_SQL];   
                sprintf(sql, "SELECT %s, %s, %s, %s FROM %s WHERE bordereau = '%s'", COLON_ETAPE, COLON_MODE, COLON_RAISON, COLON_DATE, TABLE, code);

                if (mysql_query(data->conn, sql)) 
                { 
                    sprintf(message, "Erreur requête : %s\n", mysql_error(data->conn));
                    log_line(data, message);
                    message_erreur(data, ERREUR_INTERNE);
                }

                MYSQL_RES *res = mysql_store_result(data->conn); 
                MYSQL_ROW row = mysql_fetch_row(res);

                if (atoi(row[0]) == VALUE_ETAPE_FIN)
                {
                    if (atoi(row[1]) == VALUE_MODE_REFU)
                    {
                        sprintf(message, "%s%s%s\n%s%s%s\n%s%s%s\n%s%s%s", ETAPE, DELIMITER, row[0], MODE, DELIMITER, row[1], CAUSE, DELIMITER, row[2], DATE, DELIMITER, row[3]);
                    }
                    sprintf(message, "%s%s%s\n%s%s%s\n%s%s%s\n%s%s%s", ETAPE, DELIMITER, row[0], MODE, DELIMITER, row[1], CAUSE, DELIMITER, VIDE, DATE, DELIMITER, row[3]);
                }
                else
                {
                    sprintf(message, "%s%s%s\n%s%s%s\n%s%s%s\n%s%s%s", ETAPE, DELIMITER, row[0], MODE, DELIMITER, VIDE, CAUSE, DELIMITER, VIDE, DATE, DELIMITER, row[3]);
                }
                envoier_message(data, message);
                
            }
            else
            {
                message_erreur(data, ERREUR_COLIS_INEXISTENT);
            }
        }
        else
        {
            sprintf(message, "%s%s1\n%s%s%s\n%s%s%s\n%s%s854894", ETAPE, DELIMITER, MODE, DELIMITER, VIDE, CAUSE, DELIMITER, VIDE, DATE, DELIMITER);
            envoier_message(data, message);
        }
    }
    else
    {
        if (DEBUG)
        {
            printf("[DEBUG] CODE : FALSE\n");
        }
        message_erreur(data, ERREUR_INSTRUCTION);
    }
}

bool check_code(char* code)
{
    int x, y;
    bool good;
    code[BORDEREAU_SIZE-1] = '\0';
    //printf("code : %s\n", code);
    //printf("size : %ld\n", strlen(code));

    if (code == NULL)
    {
        return false;
    }
    

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

// verifie que le colis posède une photo
void photo(SESSION *data)
{
    char *code = strtok(NULL, INSTRUCTION_DELIMITER);
    if (code == NULL)
    {
        message_erreur(data, ERREUR_INSTRUCTION);
    }

    char message[TAILLE_GRAND];
    if (check_code(code)) // verifie la forme du code
    {
        if (BDD) // si utilisation de la bdd
        {
            if (colis_existe(data, code))// verifi si le colis existe
            {

                char sql[TAILLE_SQL];   
                sprintf(sql, "SELECT %s FROM %s WHERE bordereau = '%s'", COLON_MODE, TABLE, code);

                if (mysql_query(data->conn, sql)) 
                { 
                    sprintf(message, "Erreur requête : %s\n", mysql_error(data->conn));
                    log_line(data, message);
                    message_erreur(data, ERREUR_INTERNE);
                }

                MYSQL_RES *res = mysql_store_result(data->conn); 
                MYSQL_ROW row = mysql_fetch_row(res);

                if (row[0] != NULL) // si ligne pas vide
                {
                    if (atoi(row[0]) == VALUE_MODE_ABSENT)
                    {
                        envoier_photo(data, FICHIER_PHOTO);
                    }
                    else
                    {
                        message_erreur(data, ERREUR_PHOTO_INEXISTENT);
                    }
                }
                else
                {
                    message_erreur(data, ERREUR_PHOTO_INEXISTENT);
                }
            }
            else
            {
                message_erreur(data, ERREUR_COLIS_INEXISTENT);
            }
        }
        else
        {
            envoier_photo(data, FICHIER_PHOTO);
        }
    }
    else
    {
        message_erreur(data, ERREUR_COLIS_INEXISTENT);
    }
}

// socupe d'envoier la photo
void envoier_photo(SESSION *data, char *fichier)
{
    char buff[TAILLE_PHOTO];
    char message[TAILLE_PHOTO*8];

    sprintf(message, "%s%s", PHOTO, DELIMITER);
    envoier_message(data, message);

    int fd = open(FICHIER_PHOTO, O_RDONLY);
    // boucle de lecture
    while (read(fd, buff, TAILLE_PHOTO) != 0)
    {
        encode_photo(buff, message);
        envoier_code(data, message);
    }
    envoier_message(data, "#"); // fin de limage
}


// transforme le code de l'image en binaire
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
    
    // Ajout de la fin de chaine
    int len = 0; 
    for (; src[len] != '\0'; len++); 
    des[len * 8] = '\0';
}

//------------------------------------------------------------------

// permet decrire les log
void log_line(SESSION *data, char *msg) 
{
    char timestamp[64];
    time_t now = time(NULL);
    struct tm *t = localtime(&now);

    strftime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S", t);

    fprintf(data->log, "[%s] [%s] %s\n", timestamp, data->client_ip, msg);
    //fflush(data->logf);  // pour écrire immédiatement
}

// permet de retiré les retout a la ligne et mettre des espace
void log_transforme(char *str) 
{

    for (int i = 0; str[i] != '\0'; i++) {
        if (str[i] == '\n')
            str[i] = ' ';
    }
}


// affiche le resultat de la commende help
void help() 
{
    printf("help/n");
    exit(EXIT_SUCCESS);
}