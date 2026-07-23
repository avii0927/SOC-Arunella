package com.example.arunella.repository;

import com.example.arunella.entity.Has;
import com.example.arunella.entity.HasId;
import org.springframework.data.jpa.repository.JpaRepository;

public interface HasRepository extends JpaRepository<Has, HasId> {
}
