// socupe d'envoier la photo
void envoier_photo(SESSION *data, char *fichier)
{
    char message[TAILLE_PHOTO *8+1];
    sprintf(message, "%s%s", PHOTO, DELIMITER);
    envoier_message(data, message);

    int fd = open(FICHIER_PHOTO, O_RDONLY);

    char buff;
    
    // boucle de lecture 1 par 1
    while (read(fd, &buff, 1) != 0)
    {
        chaine_en_binaire(buff, message);
        envoier_code(data, message);
    }

    envoier_message(data, "#"); // fin de limage
}

void chaine_en_binaire(const char src, char *dest)
{
    int i, bit;

    for (bit = 7; bit >= 0; bit--)
    {
        *dest = ((src >> bit) & 1) + '0';
        dest++;
    }
    *dest = '\0'; // fin de chaîne
}





void envoier_photoV2(SESSION *data, char *fichier)
{
    char message[TAILLE];
    sprintf(message, "%s%s", PHOTO, DELIMITER);
    envoier_message(data, message);

    FILE *file;
    unsigned char *buffer;
    unsigned char *code;
    long file_size;
    
    // Open image file in binary mode
    file = fopen(fichier, "r");
    if (file == NULL) {
        perror("Failed to open file");
        printf("error\n");
        fin(data);
    }

    // Get file size
    fseek(file, 0, SEEK_END);
    file_size = ftell(file);
    printf("file_size : %ld\n", file_size);
    rewind(file);

    // Allocate buffer
    code = (unsigned char *)malloc(file_size*8+1);
    buffer = (unsigned char *)malloc(file_size);
    if (buffer == NULL) {
        perror("Failed to allocate memory");
        printf("error\n");
        fclose(file);
        fin(data);
    }

    // Read file into buffer
    size_t bytes_read = fread(buffer, 1, file_size, file);
    printf("bytes_read : %ld\n", bytes_read);
    if (bytes_read != file_size) {
        perror("Failed to read file");
        printf("error\n");
        free(buffer);
        fclose(file);
        fin(data);
    }

    encode_photo(buffer, code ,bytes_read);

    int writ;
    if (writ = write(data->cnx, code, strlen(code)) == -1)
    {
        log_line(data, "[ERROR] WRITE PHOTO");
        fin(data);
    }
    printf("writ : %d\n", writ);
    envoier_message(data, "#"); // fin de limage

    // Cleanup
    free(buffer);
    fclose(file);
}



void envoier_photoV2(SESSION *data, char *fichier);
void chaine_en_binaire(const char src, char *dest);




void envoier_codeV2(SESSION *data, char *message, size_t size);
void envoier_photoV3(SESSION *data, char *fichier);