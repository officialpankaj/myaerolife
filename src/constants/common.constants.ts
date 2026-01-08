export const UserRole = {
  HERO: "0",
  MANAGER: "1",
} as const;

export type UserRole = (typeof UserRole)[keyof typeof UserRole];

export const EmployeeStatus = {
  INACTIVE: "0",
  ACTIVE: "1",
} as const;

export type EmployeeStatus = (typeof EmployeeStatus)[keyof typeof EmployeeStatus];
