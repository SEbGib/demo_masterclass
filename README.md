# Demo Masterclass - AI-Assisted Development

A demonstration project showcasing AI-assisted development workflows using specialized agents and slash commands.

## Stack

| Component | Technology |
|-----------|------------|
| **Backend** | Symfony 7.4 + API Platform 4.2 |
| **Frontend** | React 19 + TypeScript + Vite |
| **Database** | PostgreSQL 16 |
| **Container** | Docker + Docker Compose |

## Quick Start

### Prerequisites

- Docker Desktop (or Docker Engine + Compose)
- Git
- GitHub CLI (`gh`) - for issue management

### Installation

```bash
# Clone the repository
git clone https://github.com/SEbGib/demo_masterclass.git
cd demo_masterclass

# Start everything
make install

# Or step by step:
docker compose build
docker compose up -d
make db-migrate
```

### URLs

| Service | URL |
|---------|-----|
| **API Documentation** | http://localhost:8000/api |
| **Frontend** | http://localhost:5173 |
| **PostgreSQL** | localhost:5432 |

## Available Commands

```bash
# Docker management
make up          # Start all services
make down        # Stop all services
make restart     # Restart services
make logs        # View logs (follow mode)
make status      # Show container status

# Development
make shell-api   # Open shell in API container
make shell-app   # Open shell in frontend container
make shell-db    # Open PostgreSQL shell

# Testing
make test        # Run all tests
make test-api    # Run PHPUnit tests
make test-app    # Run Vitest tests

# Database
make db-migrate  # Run migrations
make db-diff     # Generate migration from entity changes
make db-reset    # Reset database completely

# GitHub Issues
make issues      # List issues
make issue ID=1  # View specific issue

# Utilities
make cache-clear # Clear Symfony cache
make api-routes  # List all API routes
```

## Project Structure

```
demo_masterclass/
├── api/                    # Symfony Backend
│   ├── src/
│   │   ├── Entity/         # Doctrine entities
│   │   └── ...
│   ├── tests/              # PHPUnit tests
│   └── composer.json
│
├── app/                    # React Frontend
│   ├── src/
│   │   ├── components/     # React components
│   │   ├── hooks/          # Custom hooks
│   │   ├── services/       # API services
│   │   └── types/          # TypeScript types
│   └── package.json
│
├── docker/                 # Docker configs
│   ├── nginx/
│   └── php/
│
├── docker-compose.yml
├── Makefile
└── README.md
```

## Features to Implement

Check the GitHub issues for features waiting to be implemented:

```bash
gh issue list
```

Each issue is designed to demonstrate different AI-assisted workflows:

- `/tdd-feature` - Test-Driven Development workflow
- `/quick-fix` - Bug fixing with automatic validation
- `@backend-expert` - Symfony/API Platform expertise
- `@react-frontend-architect` - React component development
- `@security-auditor` - Security review and audit

## AI Workflows

### TDD Feature Implementation

```
/tdd-feature Implement [feature description] as specified in issue #X
```

Workflow phases:
1. **RED** - Write failing tests first
2. **GREEN** - Implement minimal code to pass tests
3. **REFACTOR** - Improve code quality while keeping tests green

### Quick Bug Fix

```
/quick-fix Fix [bug description] as described in issue #X
```

Workflow:
1. Analyze bug and identify root cause
2. Create/update test to reproduce bug
3. Fix with automatic validation loop
4. Verify no regressions

### Agent Delegation

```
@backend-expert Add [feature] following Symfony best practices
@react-frontend-architect Create [component] with proper TypeScript types
@security-auditor Review [code/endpoint] for vulnerabilities
```

## Development Tips

### Running Tests

```bash
# API tests
make test-api

# Frontend tests
make test-app

# Watch mode for frontend
docker compose exec app pnpm test:watch
```

### Database Operations

```bash
# After modifying entities
make db-diff     # Generate migration
make db-migrate  # Apply migration

# Start fresh
make db-reset
```

### Debugging

```bash
# View container logs
make logs

# API logs only
make logs-api

# Check routes
make api-routes
```

## Contributing

This is a demo project for educational purposes. Feel free to fork and experiment!

## License

MIT
