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


base_ref=""
if git show-ref --verify --quiet refs/remotes/origin/main; then
  base_ref="origin/main"
elif git show-ref --verify --quiet refs/remotes/origin/master; then
  base_ref="origin/master"
fi

if [[ -n "$base_ref" && "$current_branch" != "HEAD" ]]; then
  commits_ahead_base="$(git rev-list --count "${base_ref}..HEAD" 2>/dev/null || echo 0)"
  if [[ "$commits_ahead_base" == "0" ]]; then
    echo -e "${YELLOW}! Grana nema novih commit-a u odnosu na ${base_ref}.${NC}"
    echo "  Bez razlike u kodu, Create PR često neće ponuditi otvaranje PR-a."
    warn=1
  else
    echo -e "${GREEN}✓ Grana ima ${commits_ahead_base} commit-a u odnosu na ${base_ref}.${NC}"
  fi
fi

if command -v gh >/dev/null 2>&1; then
  if ! gh auth status >/dev/null 2>&1; then
    echo -e "${YELLOW}! GitHub CLI nije autentifikovan (gh auth status).${NC}"
    echo "  Rešenje: gh auth login"
    warn=1
  fi
fi

echo
if [[ $fail -eq 0 && $warn -eq 0 ]]; then
  echo -e "${GREEN}Sve izgleda spremno za Create PR.${NC}"
elif [[ $fail -eq 0 ]]; then
  echo -e "${YELLOW}Repo je delimično spreman. Reši upozorenja iznad pa probaj ponovo.${NC}"
else
  echo -e "${RED}Repo NIJE spreman za Create PR dok ne rešiš greške iznad.${NC}"
  exit 1
fi