import socket
import time

SERVER_HOST = "127.0.0.1"
SERVER_PORT = 5007
CLIENTES_NECESSARIOS = 2
TOTAL_ATUALIZACOES = 5
INTERVALO_ENTRE_ATUALIZACOES = 1

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind((SERVER_HOST, SERVER_PORT))

clientes = []
clientes_registrados = set()

print(
    f"Servidor UDP unicast iniciado em {SERVER_HOST}:{SERVER_PORT}. "
    f"Aguardando {CLIENTES_NECESSARIOS} clientes..."
)

try:
    while len(clientes) < CLIENTES_NECESSARIOS:
        data, addr = sock.recvfrom(1024)
        mensagem = data.decode().strip()
        print(f"Recebido de {addr}: {mensagem}")

        if mensagem != "OK":
            print("Mensagem ignorada. Envie apenas OK para registrar o cliente.")
            continue

        if addr in clientes_registrados:
            print(f"Cliente {addr} ja registrado.")
            continue

        clientes.append(addr)
        clientes_registrados.add(addr)
        print(f"Cliente registrado: {addr} ({len(clientes)}/{CLIENTES_NECESSARIOS})")

    print("Quantidade minima de clientes atingida. Enviando atualizacoes...")

    for numero in range(1, TOTAL_ATUALIZACOES + 1):
        mensagem = f"Atualizacao {numero}"
        for cliente in clientes:
            sock.sendto(mensagem.encode(), cliente)
            print(f"Enviado para {cliente}: {mensagem}")
        time.sleep(INTERVALO_ENTRE_ATUALIZACOES)

except KeyboardInterrupt:
    print("\nServidor encerrado.")
finally:
    sock.close()
