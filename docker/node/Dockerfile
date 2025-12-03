FROM node:20-alpine

# Install pnpm
RUN corepack enable && corepack prepare pnpm@latest --activate

# Set working directory
WORKDIR /app

# Expose Vite dev server port
EXPOSE 5173

# Install dependencies and start dev server
CMD ["sh", "-c", "pnpm install && pnpm dev --host 0.0.0.0"]
