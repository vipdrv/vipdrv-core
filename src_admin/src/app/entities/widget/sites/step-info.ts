import { Variable, WorkingInterval } from './../../../utils/index';

export class StepInfo {
    name: string;
    localizationKey: string;
    isActive: boolean;
    order: number;
    entitiesCount: number;
    activeEntitiesCount: number;
    activeEntitiesLocalizationKey: string;
    noActiveEntitiesLocalizationKey: string;
    notActiveEntitiesLocalizationKey: string;
}