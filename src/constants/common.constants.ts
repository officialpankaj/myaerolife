export const UserRole = {
  HERO: "0",
  MANAGER: "1",
  SUPER_MANAGER: "2",
} as const;

export type UserRole = (typeof UserRole)[keyof typeof UserRole];

export const EmployeeStatus = {
  INACTIVE: "0",
  ACTIVE: "1",
} as const;

export type EmployeeStatus =
  (typeof EmployeeStatus)[keyof typeof EmployeeStatus];

export const LunchStatus = {
  LUNCHED_TODAY: "0",
  RELAUNCHING: "1",
} as const;

export const LunchStatusName = {
  [LunchStatus.LUNCHED_TODAY]: "Lunched Today",
  [LunchStatus.RELAUNCHING]: "Relaunching with input",
};

export type LunchStatus = (typeof LunchStatus)[keyof typeof LunchStatus];
