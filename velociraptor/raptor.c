#include "constante.h"
#include "fonction.h"




int main(int argc, char *argv[])
{
    // initalisation des variable
    char *chemain = DEFAULT_LOGIN;
    bool colisInfinit = false;
    int port, nbColisMax;
    char *portC, *nbColisMaxC;

    int pid;
    int sock;
    int opt;
    int ret;
    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;
    COMPTE *c = NULL;
    int size = sizeof(conn_addr);
    int cnx; // le file descriptor du sock
    MYSQL *conn;
    SESSION data;
    data.login = NULL;
    data.bdd = BDD;
    data.debug = DEBUG;

//-------------------------------------------------------

    //oberture des fichier
    FILE *logI = fopen(INIT_FILE, "w");
    FILE *logC = fopen(LOG_FILE, "w");

    // permer lécriture en directre
    setvbuf(logI, NULL, _IONBF, 0);
    setvbuf(logC, NULL, _IONBF, 0);
    data.log = logC;
    
//-------------------------------------------------------

    //defénition des parametre attendu
    struct option long_options[] = 
    { 
        {"help", no_argument, 0, 'h'}, 
        {"account", required_argument, 0, 'a'}, 
        {"nbcolis", required_argument, 0, 'n'}, 
        {"port", required_argument, 0, 'p'},
        {"bdd", no_argument, 0, 'b'}, 
        {"debug", no_argument, 0, 'd'}, 
        {0, 0, 0, 0} 
    };

    // recupération des parametre
    while ((opt = getopt_long(argc, argv, "ha:n:p:bd", long_options, NULL)) != -1)
    { 
        switch (opt) 
        { 
            case 'h': 
                fprintf(logI, "[PARAMETRE] -h\n");
                fclose(logI);
                fclose(logC);
                help();
                break; 
                
            case 'a': 
                chemain = optarg;
                fprintf(logI, "[PARAMETRE] -a : %s\n", chemain);
                break; 
            
            case 'n':
                nbColisMaxC = optarg;
                fprintf(logI, "[PARAMETRE] -n : %s\n", nbColisMaxC);
                break;

            case 'p':
                portC = optarg;
                fprintf(logI, "[PARAMETRE] -p : %s\n", portC);
                break;

            case 'b':
                data.bdd = false;
                fprintf(logI, "[PARAMETRE] -b\n");
                break; 

            case 'd':
                data.debug = true;
                fprintf(logI, "[PARAMETRE] -d\n");
                break; 
                
            case '?': 
                fprintf(logI, "[FATAL] Option inconnue: -%c\n", optopt); 
                exit(EXIT_FAILURE);
                break;
        } 
    }

//---------------------------------------------------------------------------------------------

    
    srand(time(NULL)); // aléatoire du borderau
    signal(SIGCHLD, tombe); // eviter les enfant zombie
    fprintf(logI, "%s START\n", SERVER);


    // recupération des compte
    c = init_compte(chemain, logI);
    fprintf(logI ,"%s SUCCESS INIT COMPTE\n", SERVER);
    if (data.debug)
    {
        fprintf(logI, "[DEBUG] COMPTE :\n");
        affiche_compte(c, logI);
    }

    // inisalisation avec la bdd
    conn = mysql_init(NULL);
    if(data.bdd)
    {
        init_bdd(conn, logI);
        data.conn = conn;
    }

//-------------------------------------------------------


    // verifie que le nombre de colie est bon
    nbColisMax = atoi(nbColisMaxC);
    if (nbColisMax == 0 || nbColisMax < -1)
    {
        fprintf(logI, "[FATAL] Nombre de colis incorecte.\n");
        exit(EXIT_FAILURE);
    }
    else if (nbColisMax == -1)
    {
        colisInfinit = true;
        fprintf(logI,"%s SUCCESS COLIS SET INFINIT\n", SERVER);
    }
    else
    {
        fprintf(logI,"%s SUCCESS COLIS SET %d\n", SERVER, nbColisMax);
    }
    
    // verifie que port est bon
    port = atoi(portC);
    if (port <= 0)
    {
        fprintf(logI, "[FATAL] Port incorecte.\n");
        exit(EXIT_FAILURE);
    }
    else
    {
        fprintf(logI,"%s SUCCESS PORT SET %d\n", SERVER, port);
    }

//-------------------------------------------------------


    // mise en place du socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    addr.sin_addr.s_addr = inet_addr(IP); 
    addr.sin_family = AF_INET;
    addr.sin_port = htons(port);
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));

    opt = 1;
    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt)) == -1) {
        fprintf(logI, "[ERROR] setsockopt.\n");
        exit(EXIT_FAILURE);
    }
    fprintf(logI,"%s SUCCESS INIT SOCKET\n", SERVER);

    fprintf(logI,"%s READY\n", SERVER);
    fclose(logI);

//---------------------------------------------------------------------------------------------

    // boucle que mode server
    while (true)
    {
        // a temps une demande
        if(listen(sock, 1) == -1)
        {
            fprintf(data.log, "[ERROR] : %d", errno);
        }
        else
        {
            // le file descriptor du sock
            cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
            if (cnx == -1)
            {
                fprintf(data.log, "[ERROR] : %d", errno);
            }
            else
            {
                pid = fork();
                if (pid == -1)
                {
                    fprintf(data.log, "[ERROR] : %d", errno);
                }
                else if (pid == 0) // fils
                {
                    char client_ip[INET_ADDRSTRLEN]; 
                    inet_ntop(AF_INET, &conn_addr.sin_addr, client_ip, sizeof(client_ip));
                    data.cnx = cnx;
                    strcpy(data.client_ip , client_ip);

                    close(sock);
                    
                    comminication(&data, c, colisInfinit, nbColisMax);
                    fin(&data);
                }
            }
            
        }
    }
    
    
    return EXIT_SUCCESS;
}

