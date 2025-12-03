FROM node:20-alpine

# Install pnpm
RUN corepack enable && corepack prepare pnpm@latest --activate

# Set working directory
WORKDIR /app

# Expose Vite dev server port
EXPOSE 5173

# Default command - run Vite dev server
CMD ["pnpm", "dev", "--host", "0.0.0.0"]
