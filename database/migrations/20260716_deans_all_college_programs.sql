-- Dean access is scoped by college, never by an individual program.
UPDATE academic_teachers SET program = NULL;
