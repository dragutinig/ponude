#!/usr/bin/env bash
set -euo pipefail

FIX_MODE="${1:-}"
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
fail=0
warn=0

current_branch="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || true)"
if [[ -z "$current_branch" || "$current_branch" == "HEAD" ]]; then
  echo -e "${RED}✗ Nisi na lokalnoj grani (detached HEAD).${NC}"
  fail=1
else
  echo -e "${GREEN}✓ Trenutna grana: ${current_branch}${NC}"
  if [[ "$current_branch" == "main" || "$current_branch" == "master" ]]; then
    echo -e "${YELLOW}! Na grani si ${current_branch}. Create PR često očekuje feature granu.${NC}"
    echo "  Preporuka: git switch -c fix/<kratak-opis>"
    warn=1
  fi
fi

if git remote get-url origin >/dev/null 2>&1; then
  origin_url="$(git remote get-url origin)"
  echo -e "${GREEN}✓ origin je podešen: ${origin_url}${NC}"
else
  echo -e "${RED}✗ Nedostaje remote 'origin'. Bez njega Create PR uglavnom pada.${NC}"
  echo "  Rešenje: git remote add origin <URL_REPO>"
  fail=1
fi

if [[ "$current_branch" != "HEAD" ]] && git rev-parse --abbrev-ref --symbolic-full-name "@{u}" >/dev/null 2>&1; then
  upstream="$(git rev-parse --abbrev-ref --symbolic-full-name "@{u}")"
  echo -e "${GREEN}✓ Upstream postoji: ${upstream}${NC}"
else
  if [[ "$current_branch" != "HEAD" ]] && git remote get-url origin >/dev/null 2>&1; then
    echo -e "${YELLOW}! Grana nema upstream.${NC}"
    if [[ "$FIX_MODE" == "--fix" ]]; then
      if git push -u origin "$current_branch"; then
        echo -e "${GREEN}✓ Automatski je podešen upstream preko git push -u.${NC}"
      else
        echo -e "${RED}✗ Nije uspeo git push -u origin ${current_branch}.${NC}"
        fail=1
      fi
    else
      echo "  Rešenje: git push -u origin ${current_branch}"
      warn=1
    fi
  fi
fi

if [[ -n "$(git status --porcelain)" ]]; then
  echo -e "${YELLOW}! Imaš necommitovane izmene.${NC}"
  git status --short | sed 's/^/    /'
  echo "  Rešenje: git add -A && git commit -m \"poruka\""
  warn=1
else
  echo -e "${GREEN}✓ Working tree je čist.${NC}"
fi

ahead_behind="$(git rev-list --left-right --count HEAD...@{u} 2>/dev/null || true)"
if [[ -n "$ahead_behind" ]]; then
  ahead="${ahead_behind##* }"
  behind="${ahead_behind%% *}"
  if [[ "$ahead" != "0" ]]; then
    echo -e "${YELLOW}! Imaš ${ahead} lokalnih commit-a koji nisu push-ovani.${NC}"
    echo "  Rešenje: git push"
    warn=1
  else
    echo -e "${GREEN}✓ Nema nepush-ovanih commit-a.${NC}"
  fi
fi

echo
if [[ $fail -eq 0 && $warn -eq 0 ]]; then
  echo -e "${GREEN}Sve izgleda spremno za Create PR.${NC}"
elif [[ $fail -eq 0 ]]; then
  echo -e "${YELLOW}Repo je delimično spreman. Reši upozorenja iznad pa probaj ponovo.${NC}"
else
  echo -e "${RED}Repo NIJE spreman za Create PR dok ne rešiš greške iznad.${NC}"