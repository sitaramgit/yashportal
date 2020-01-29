import { TestBed } from '@angular/core/testing';

import { GetModulesService } from './get-modules.service';

describe('GetModulesService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: GetModulesService = TestBed.get(GetModulesService);
    expect(service).toBeTruthy();
  });
});
