import socket

SERVER_HOST = "127.0.0.1"
SERVER_PORT = 5007
TOTAL_ATUALIZACOES_ESPERADAS = 5

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

try:
    sock.sendto(b"OK", (SERVER_HOST, SERVER_PORT))
    print(f"Sinal OK enviado para {SERVER_HOST}:{SERVER_PORT}.")
    print("Aguardando atualizacoes...")

    for _ in range(TOTAL_ATUALIZACOES_ESPERADAS):
        data, addr = sock.recvfrom(1024)
        print(f"Recebido de {addr}: {data.decode()}")

except KeyboardInterrupt:
    print("\nCliente encerrado.")
finally:
    sock.close()
